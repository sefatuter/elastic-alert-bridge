/elastalert/config.yaml
```yaml
# config.yaml

# 1) Elasticsearch connection
es_host: localhost
es_port: 9200
es_username: elastic
es_password: ""
es_auth_type: basic
use_ssl: false
verify_certs: false 

# 2) ElastAlert writeback index
writeback_index: elastalert_status

# 3) Where your rule files live
rules_folder: rules

# 4) When to run - Check every 30 seconds for testing
run_every:
  seconds: 30

# How far back to look for events in each run - Look back 1 minute
buffer_time:
  minutes: 1

# 5) Default timestamp field in your docs
timestamp_field: "@timestamp"

# 6) Alert settings
alert_text_type: alert_text_only

# 7) Debug mode for testing
debug: true

# 8) Logging settings
log_level: info
log_file: elastalert.log

# 9) Maximum number of alerts to send per rule
max_query_size: 1000

# 10) Time to wait before retrying on failure
retry_after: 30

```


/etc/filebeat/filebeat.yml                                                     
```yml
filebeat.inputs:
  - type: log
    enabled: true
    paths:
      - /var/lib/postgresql/16/main/pg_log/*.csv
    multiline.pattern: '^\d{4}-\d{2}-\d{2}'
    multiline.negate: true
    multiline.match: after
    fields:
      log_type: postgresql
    fields_under_root: true

setup.ilm.enabled: false
setup.template.name: "postgres-logs"
setup.template.pattern: "postgres-logs-*"

output.elasticsearch:
  hosts: ["http://localhost:9200"]
  index: "postgres-logs-%{+yyyy.MM.dd}"
  username: "elastic"
  password: ""
```

/etc/elasticsearch/elasticsearch.yml
```yml
xpack.security.enabled: true
xpack.security.enrollment.enabled: true
xpack.security.http.ssl.enabled: false
```

.env
```
# Elasticsearch Configuration
ELASTICSEARCH_HOST=localhost:9200
ELASTICSEARCH_USERNAME=elastic
ELASTICSEARCH_PASSWORD=
ELASTICSEARCH_INDEX=postgresql-logs
```

- Get password: sudo /usr/share/elasticsearch/bin/elasticsearch-reset-password -u elastic