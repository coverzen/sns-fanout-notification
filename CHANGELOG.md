# [2.0.0](https://github.com/coverzen/sns-fanout-notification/compare/v1.0.0...v2.0.0) (2026-06-24)


* feat!: add Laravel 13 support and drop Laravel 11 ([b9f0aa9](https://github.com/coverzen/sns-fanout-notification/commit/b9f0aa9e69e9d24f6fd9ac01a5ce476de5d896ce))


### BREAKING CHANGES

* Laravel 11 is no longer supported; the illuminate
constraint moves from ^11.0||^12.0 to ^12.0||^13.0 and the minimum PHP is
now 8.3. Consumers must run Laravel 12 or 13 on PHP 8.3+.

# 1.0.0 (2025-09-15)


### Features

* Initial release ([8218a53](https://github.com/coverzen/sns-fanout-notification/commit/8218a537e013306da02740f19c4c92a4ca5ee9de))
