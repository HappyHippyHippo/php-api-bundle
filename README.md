# api-bundle

![Code quality](https://github.com/HappyHippyHippo/php-api-bundle/workflows/Code%20Quality/badge.svg)

### configuration environment variables

##### application information
- HIPPY_APP_ID
    - string
- HIPPY_APP_NAME
    - string
- HIPPY_APP_VERSION
    - string

##### Version handling
- HIPPY_VERSION_HEADER_ENABLED
    - boolean

##### request allow/deny configuration
- HIPPY_ACCESS_ALLOW_GLOBAL
- HIPPY_ACCESS_ALLOW_ENDPOINT
- HIPPY_ACCESS_DENY_GLOBAL
- HIPPY_ACCESS_DENY_ENDPOINT

##### CORS configuration
- HIPPY_CORS_ENABLED
    - boolean
- HIPPY_CORS_ORIGIN
    - string

##### Config endpoint
- HIPPY_ENDPOINT_CONFIG_ENABLED
    - boolean

##### OpenAPI endpoint
- HIPPY_ENDPOINT_OPENAPI_ENABLED
    - boolean
- HIPPY_ENDPOINT_OPENAPI_SOURCE
    - string
- HIPPY_ENDPOINT_OPENAPI_SERVERS
    - comma seperated string list

##### Error management
- HIPPY_ERROR_TRACE_ENABLED
    - boolean

##### Logging
- HIPPY_LOG_REQUEST_ENABLED
    - boolean
- HIPPY_LOG_REQUEST_MESSAGE
    - string
- HIPPY_LOG_REQUEST_LEVEL
    - string
- HIPPY_LOG_RESPONSE_ENABLED
    - boolean
- HIPPY_LOG_RESPONSE_MESSAGE
    - string
- HIPPY_LOG_RESPONSE_LEVEL
    - string
- HIPPY_LOG_EXCEPTION_ENABLED
    - boolean
- HIPPY_LOG_EXCEPTION_MESSAGE
    - string
- HIPPY_LOG_EXCEPTION_LEVEL
    - string

##### Redis configuration
- HIPPY_REDIS_DSN
    - string
