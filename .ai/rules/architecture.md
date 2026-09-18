# Architecture and Operating Rules

These are standing project decisions. Revisit them only when requirements or
official guidance changes, and record the trade-off when doing so.

## Repository and application boundary

- The canonical repository is `https://github.com/Mohammad-Ranjbar/laravel-tricks`.
- This is a backend-only Laravel API. Do not add a bundled React, Inertia, or
  other application frontend unless the user explicitly changes this decision.
- Application requests must remain stateless. Do not use cookie/session
  authentication or `remember_token` for API clients.
- Do not start the application server or Horizon automatically; the user runs
  long-lived local processes.

## Authentication and OAuth2

- Use Laravel Passport as the OAuth2 server and the `api` guard as the runtime
  authentication default.
- Access tokens expire after 15 minutes and refresh tokens after 30 days unless
  a documented product or threat-model decision changes those values.
- Prefer Authorization Code with PKCE for public browser or mobile clients.
  Never enable Password Grant or Implicit Grant merely to simplify a first-party
  login flow; both require an explicit security review and documented reason.
- Do not create an OAuth client until its client type, name, redirect URIs, and
  ownership are known. Public PKCE clients must not be issued a usable secret.
- Send access tokens as Bearer tokens. Never log access tokens, refresh tokens,
  authorization codes, client secrets, or Passport private keys.
- Passport encryption keys are deployment secrets and must remain outside Git.
- Passport's future authorization/consent interaction and Horizon's dashboard
  are operational/auth-server concerns, not application frontend features.
  Production dashboard access must be explicitly authorized; default-deny is
  required until that policy exists.

## Data, queues, and Horizon

- PostgreSQL is the primary relational database.
- Redis powers application queues and cache. Keep cache and queue workloads on
  their dedicated configured Redis logical databases.
- Laravel Horizon is the required queue supervisor and monitoring interface.
  Production must run it under a process monitor and restart it during deploys.
- Job payloads run through Redis. Job batch metadata is intentionally stored in
  PostgreSQL's `job_batches` table so batch progress and cancellation survive
  worker restarts.
- Keep queue jobs idempotent where practical. Set explicit timeouts/retries for
  external side effects and design batch failure/cancellation behavior before
  dispatching production workloads.

## Local PHP runtime

- Manage PHP for this project exclusively through Homebrew. Do not use, inspect,
  configure, or rely on Laravel Herd PHP runtimes.
- This development machine intentionally uses `memory_limit=-1` only for PHP
  versions that are actually installed through Homebrew. Do not modify leftover
  configuration directories for PHP versions that Homebrew does not report as
  installed.
- Unlimited PHP memory is not a production default. Horizon workers retain
  explicit per-worker memory limits, and production PHP limits must be sized
  from workload measurements to reduce denial-of-service and runaway-job risk.
