## Executive Summary

The Agent Platform is currently deployed on the same server as the main Advisors Academy application, sharing:

* PHP-FPM worker pool
* MySQL database server
* Redis instance
* Server CPU and memory resources

As a result, performance issues or traffic spikes on either platform can directly impact the other. While the application codebase is generally well structured, several critical performance and security concerns should be addressed before scaling internationally.

## Infrastructure and Architecture Concerns

### Shared Infrastructure

The platform currently operates on shared infrastructure with the main Advisors Academy application.

Key implications include:

| Component           | Current State           | Risk                                        |
| ------------------- | ----------------------- | ------------------------------------------- |
| Application Servers | Shared                  | Resource contention between platforms       |
| PHP-FPM Workers     | Shared pool (5 workers) | Limited concurrent request handling         |
| MySQL               | Shared instance         | Competing workloads and connection usage    |
| Redis               | Shared instance         | Underutilized and not leveraged effectively |

Any significant growth in either application will impact the performance of the other.

## Nginx Configuration Findings

### Missing Upload Configuration

The Agent Platform accepts document uploads such as:

* Passports
* Academic transcripts
* Supporting PDFs

However, the Nginx configuration does not explicitly define:

```nginx
client_max_body_size
```

As a result, uploads larger than the default 1 MB limit may fail with HTTP 413 errors.

### Additional Web Server Gaps

The platform also shares several configuration weaknesses identified in the main application:

* No request rate limiting
* No optimized gzip compression settings
* No upstream keepalive configuration for PHP-FPM

### Critical Security Concern: OTP Endpoint

The endpoint:

```text
/api/auth/otp
```

currently has no request throttling.

An attacker can repeatedly call this endpoint to:

* Exhaust email delivery quotas
* Generate excessive database writes
* Use the service as a spam relay
* Create unnecessary infrastructure costs

This represents an active attack surface that should be addressed immediately.

## Session, Cache, and Queue Configuration

Current configuration:

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

While database-backed sessions are an improvement over file-based sessions, this configuration still places unnecessary load on MySQL.

Redis is installed and operational but is not being used for caching or queue processing.

For a JWT-based API, session storage requirements are minimal, making Redis an ideal solution for:

* Cache storage
* Queue processing
* Temporary authentication data
* Rate limiting

## Application-Level Performance Findings

### 1. Unbounded Data Retrieval

The most significant code-level concern exists within:

```php
AdminDashboardController::recentActivity()
```

Current implementation:

```php
StudentApplications::orderBy('updated_at', 'desc')->get();
```

This retrieves every application record into memory on every request.

Current impact is minimal due to the relatively small dataset, but as application volume grows, memory usage and response times will increase dramatically.

Recommended fix:

```php
StudentApplications::orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();
```

Notably, the equivalent agent dashboard implementation already follows this approach correctly.

### 2. Excessive Dashboard Queries

The dashboard currently generates an unusually high number of database queries.

#### Trend Reporting

The `applicationsTrend()` method executes:

* 7–10 date iterations
* Multiple status-specific COUNT queries per iteration

Resulting in approximately:

* 49 queries per dashboard request
* 70 queries for administrative dashboards

#### Statistics Endpoint

The `stats()` method performs multiple independent COUNT queries rather than aggregating results through a single grouped query.

#### Monthly Reporting

The following methods execute one query per month:

* `monthlyApplications()`
* `monthlyAgentRegistration()`

Combined, a single dashboard load can trigger more than 100 database queries.

Recommended approach:

Use aggregated queries such as:

```sql
SELECT status, COUNT(*)
FROM student_applications
GROUP BY status;
```

and

```sql
SELECT DATE(created_at), status, COUNT(*)
FROM student_applications
GROUP BY DATE(created_at), status;
```

This would reduce query volume dramatically while improving response times.

### 3. OTP Table Growth

OTP records currently use soft deletes.

When OTPs expire:

* Records remain in the database.
* Only the `deleted_at` field is populated.
* No automated cleanup process exists.

Although current growth is manageable, large-scale adoption could generate thousands of OTP records daily.

Recommended action:

Implement scheduled pruning:

```php
OTP::where('expires_at', '<', now())
    ->forceDelete();
```

on a daily basis.

### 4. Missing Authentication Rate Limiting

The following public endpoints currently have no throttling:

* `/api/auth/register`
* `/api/auth/login`
* `/api/auth/otp`
* `/api/auth/reset-password`

This leaves the platform vulnerable to:

* Brute-force attacks
* Credential stuffing
* Email abuse
* Automated account creation

Recommended minimum limits:

* OTP endpoint: `throttle:5,1`
* Login endpoint: `throttle:10,1`

### 5. Courses API Pagination Risk

Current implementation:

```php
$per_page = $request->input('per_page', 100);
```

Issues:

* Default page size is already large.
* No maximum limit is enforced.
* A client can request extremely large result sets.

Examples:

```text
?per_page=100
```

returns 100 records by default.

```text
?per_page=100000
```

could potentially request the entire dataset.

Recommended approach:

Cap results through validation or logic such as:

```php
$per_page = min($per_page, 50);
```

### 6. Cross-Database Coupling

The Agent Platform retrieves course and university data directly from the main Advisors Academy database via the `advisor_db` connection.

This creates tight coupling between the two systems:

* Shared database connections
* Shared resource consumption
* Shared performance bottlenecks

As either platform grows, they increasingly compete for the same database resources.

Long-term, this architecture should be replaced with either:

* API-based data access from the main platform, or
* A synchronized read-only database replica

## Positive Findings

The platform also demonstrates several strengths that support future growth:

* Strong indexing strategy across key tables.
* Stateless JWT authentication architecture.
* Proper use of database transactions during application creation.
* Pagination implemented across primary listing endpoints.
* Role-based data access controls are correctly enforced.
* Middleware separation between administrative and agent functionality.

These foundations significantly reduce future scaling complexity.

## Recommended Action Plan

### Immediate Priority Fixes

The following changes can be implemented immediately with minimal effort:

1. Add `limit(5)` to `AdminDashboardController::recentActivity()`.
2. Consolidate dashboard COUNT queries into grouped aggregate queries.
3. Add throttle middleware to authentication endpoints.
4. Implement automated OTP pruning.

### Short-Term Improvements

5. Migrate cache and queues to Redis.
6. Enforce maximum page sizes on courses endpoints.
7. Configure `client_max_body_size 20M` for document uploads.

### Long-Term Architecture Improvements

8. Remove direct database coupling between the Agent Platform and Advisors Academy by introducing API-based access or a dedicated read replica.

## Conclusion

The Agent Platform has a solid architectural foundation and benefits from a stateless API design that is inherently easier to scale than traditional session-based applications. However, several code-level inefficiencies, missing rate limits, and infrastructure dependencies must be addressed before supporting significant international growth.

The highest-priority items are dashboard query optimization, authentication throttling, OTP cleanup, Redis adoption, and reducing database coupling between platforms. Addressing these areas will substantially improve both performance and operational resilience.
