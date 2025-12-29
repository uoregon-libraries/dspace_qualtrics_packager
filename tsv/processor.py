import sys
import os
import shutil
def get_full_record_class(projname):
    """Dynamically imports the FullRecord class from the project's module."""
    try:
        module = __import__(f"{projname}.full_record", fromlist=['FullRecord'])
        return module.FullRecord
    except ImportError as e:
        print(f"Error: Could not import FullRecord for project '{projname}'.")
        raise e

def create_record(line: str, projname: str, FullRecord) -> 'FullRecord':
    """Creates and initializes a FullRecord object."""
    record = FullRecord()
    record.init(projname)
    record.get_configs()
    record.set_metadata(line)
    return record

def create_dir(dirname: str, workpath: str) -> str:
    """Creates a directory for the record in the work path."""
    print(f"making {dirname}")
    recorddir = os.path.join(workpath, dirname)
    if os.path.isdir(recorddir):
        raise FileExistsError(f"Possible duplicate: {dirname}")
    os.makedirs(recorddir)
    return recorddir + os.path.sep

def write_xml(record, recordpath: str):
    """Writes the Dublin Core XML file."""
    content = record.assemble_properties()
    xml_filepath = os.path.join(recordpath, "dublin_core.xml")
    with open(xml_filepath, 'w', encoding='utf-8') as f:
        f.write(content)

def write_contents(record, recordpath: str):
    """Writes the contents file"""
    fpath = os.path.join(recordpath, "contents")
    with open(fpath, 'w', encoding='utf-8') as f:
      f.write(f"{record.filename['val']}\tbundle:ORIGINAL\n")
      f.write("license.txt\tbundle:LICENSE\n")

def copy_license(recordpath: str):
    curdir = os.getcwd()
    license_path = os.path.join(curdir, "license.txt")
    dest_path = os.path.join(recordpath, "license.txt")
    shutil.copy(license_path, dest_path)

def copy_content_file(record, recordpath: str, filepath: str):
    """Copies the content file associated with the record."""
    try:
        filename = record.filename['val']
        source_path = os.path.join(filepath, filename)
        dest_path = os.path.join(recordpath, filename)
        if os.path.exists(source_path):
            shutil.copy(source_path, dest_path)
        else:
            print(f"Warning: Content file not found at {source_path}")
    except AttributeError:
        print(f"Warning: Content file not found at {source_path}")

def process_everything(projname: str, datafile: str, FullRecord):
    """Processes all lines in the data file."""
    curdir = os.getcwd()
    projpath = os.path.join(curdir, projname)
    workpath = os.path.join(projpath, "work")
    filepath = os.path.join(projpath, "files")
    
    os.makedirs(workpath, exist_ok=True)
    
    data_file_path = os.path.join(projpath, datafile)
    try:
        with open(data_file_path, 'r', encoding='utf-8') as f:
            for line in f:
                line = line.strip()
                if not line:
                    continue
                try:
                    record = create_record(line, projname, FullRecord)
                    record_dir_path = create_dir(record.dirname['val'], workpath)
                    write_xml(record, record_dir_path)
                    copy_content_file(record, record_dir_path, filepath)
                    copy_license(record_dir_path)
                    write_contents(record, record_dir_path)
                except Exception as e:
                    print(f"Error processing record: {e}\nRecord data: {line[:100]}...")

    except FileNotFoundError:
        print(f"Error: Data file not found at {data_file_path}")
        sys.exit(1)

def main():
    """Main function to run the processor from the command line."""
    if len(sys.argv) != 3:
        print("Usage: python processor.py <projectname> <datafile>")
        sys.exit(1)
    
    projname = sys.argv[1]
    datafile = sys.argv[2]
    FullRecord = get_full_record_class(projname)

    process_everything(projname, datafile, FullRecord)

if __name__ == "__main__":
    main()
