import sys
import os
import shutil
from chc.full_record import FullRecord

def create_record(line: str, projname: str) -> FullRecord:
    """Creates and initializes a FullRecord object."""
    if projname != 'chc':
        raise ValueError(f"This processor is currently configured only for the 'chc' project.")
    
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

def write_xml(record: FullRecord, recordpath: str):
    """Writes the Dublin Core XML file."""
    content = record.assemble_properties()
    xml_filepath = os.path.join(recordpath, "dublin_core.xml")
    with open(xml_filepath, 'w', encoding='utf-8') as f:
        f.write(content)

def copy_content_file(record: FullRecord, recordpath: str, filepath: str):
    """Copies the content file associated with the record."""
    if hasattr(record, 'filename') and record.filename and 'val' in record.filename and record.filename['val']:
        original_filename = record.filename['val']
        new_filename = original_filename.replace(" ", "_")
        
        source_path = os.path.join(filepath, original_filename)
        dest_path = os.path.join(recordpath, new_filename)
        
        if os.path.exists(source_path):
            shutil.copy(source_path, dest_path)
        else:
            print(f"Warning: Content file not found at {source_path}")

def process_everything(projname: str, datafile: str):
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
                    record = create_record(line, projname)
                    record_dir_path = create_dir(record.construct_dirname(), workpath)
                    write_xml(record, record_dir_path)
                    copy_content_file(record, record_dir_path, filepath)
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
    
    process_everything(projname, datafile)

if __name__ == "__main__":
    main()
