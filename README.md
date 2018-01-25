[![Build Status](https://travis-ci.org/NINEJKH/myformer.svg?branch=master)](https://travis-ci.org/NINEJKH/myformer)

# myformer

## Installation

```bash
$ curl -#fL "$(curl -s https://api.github.com/repos/NINEJKH/myformer/releases/latest | grep 'browser_download_url' | sed -n 's/.*"\(http.*\)".*/\1/p')" | sudo tee /usr/local/bin/myformer > /dev/null && sudo chmod +x /usr/local/bin/myformer
```

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
