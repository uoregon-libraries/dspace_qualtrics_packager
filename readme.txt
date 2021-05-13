Prep:
  1. metadata file
    convert the metadata into a tab-separated file containing the required fields
    save this file here: <projectname>/data.csv
  2. content files
    place these in <projectname>/files
  3. update project config file
    confirm that fields, indices, static values are all correct.
  4. optional test:
    php test_<projectname>.php
  
Process:
  1. php processor.php <projectname>
  2. php postprocess.php <projectname> <comma-separated list of exts> //eg pdf,m4v
  3. sh zip_all.sh <projectname>/work
  4. upload all zips to scholarsbank server and ingest
