# myformer

## Example

create structure dump

```bash
$ mysqldump \
  --verbose \
  --compress \
  --no-data \
  --quick \
  database \
  > database_structure.sql
```

create data dump

```bash
$ mysqldump \
  --verbose \
  --compress \
  --complete-insert \
  --no-create-info \
  --quick \
  --hex-blob \
  database \
  > database_data.sql
```

create config

```bash
$ cat <<'EOF' > .myform.json
{
    "table_name": [{
            "columnA": {
                "Tel": []
            } 
        },
        {
            "columnB": {
                "Email": []
            }
        },
        {
            "columnC": {
                "Replace": ["foo", "bar"]
            }
        }
    ]
}
EOF
```

anonymise data

```bash
$ myformer
```
