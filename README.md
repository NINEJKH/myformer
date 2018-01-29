[![Build Status](https://travis-ci.org/NINEJKH/myformer.svg?branch=master)](https://travis-ci.org/NINEJKH/myformer)

# myformer

## Installation

```bash
$ curl -#fL "$(curl -s https://api.github.com/repos/NINEJKH/myformer/releases/latest | grep 'browser_download_url' | sed -n 's/.*"\(http.*\)".*/\1/p')" | sudo tee /usr/local/bin/myformer > /dev/null && sudo chmod +x /usr/local/bin/myformer
```

## Example

### create structure dump

```bash
$ mysqldump \
  --verbose \
  --compress \
  --no-data \
  --quick \
  database \
  > database_structure.sql
```

### create data dump

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

### create config

```bash
$ cat <<'EOF' > .myform.json
{
    "table_name": [{
            "columnA": {
                "Tel": null
            } 
        },
        {
            "columnB": {
                "Email": "qa+%s@company.com"
            }
        },
        {
            "columnC": {
                "Set": "static content"
            }
        }
    ]
}
EOF
```

### anonymise data

```bash
$ myformer transform *_data.sql
```

This will create a file with the same name + postfixed with "+transformed".

## Rules

### Email

Anonymise Email addresses.

Accepts a string parameter, `%s` will be replaced with the first 16 chars
of the md5 hash of the original value. 

### Ref

Set column value from another column value.

Accepts a string parameter, which will be used as column name of the
source value.

### Set

Statically set the value of the string parameter

### Tel

Random telphone-like number (999 + 10-digits)
