import unittest
import os
import shutil
import processor

class TestEndToEnd(unittest.TestCase):

    def setUp(self):
        """Set up a test environment."""
        self.proj_name = 'chc'
        self.work_dir = os.path.join(self.proj_name, 'work')
        
        # Create dummy project structure
        os.makedirs(self.work_dir, exist_ok=True)
        
        # Create dummy config file
        with open(os.path.join(self.proj_name, 'config'), 'w') as f:
            f.write("""
{
    "title": {"ind": 3},
    "authors": {"ind": [1, 2]},
    "advisors": {"ind": [12, 13]},
    "subjects": {"ind": [5, 9]},
    "abstract": {"ind": 10},
    "issued": {"val": "2025"},
    "rights": {"ind": 21},
    "publisher": {"val": "University of Oregon"},
    "lang": {"val": "en_US"},
    "type": {"val": "Dissertation or thesis"},
    "permission": {"ind": 23},
    "embargo": {"ind": 20},
    "orcid": {"ind": 22}
}
            """)

        # Sample data as a tab-separated string
        self.sample_data = (
            "email@example.com\tJohn\tDoe\tA Great Thesis\tComputer Science\t"
            "AI\tMachine Learning\tNLP\tRobotics\tVision\t"
            "This is the abstract of the great thesis.\tPhD\tJane\tSmith\t"
            "CAS\tCS\t2025-05-15\t-\t-\t-\t"
            "restrict access\tAll rights reserved.\t1234-5678-9012-3456\tYes\t"
            "MyThesis.pdf\t-\t-"
        )
        
        # Create dummy data file
        self.data_filename = 'data.tsv'
        with open(os.path.join(self.proj_name, self.data_filename), 'w') as f:
            f.write(self.sample_data)

    def test_processor_end_to_end(self):
        """Test the full processing pipeline for chc."""
        # Run the processor
        processor.process_everything(self.proj_name, self.data_filename)
        
        # Check output directory
        output_dir = os.path.join(self.work_dir, 'DoeJohn')
        self.assertTrue(os.path.isdir(output_dir))
        
        # Check for dublin_core.xml
        xml_file = os.path.join(output_dir, 'dublin_core.xml')
        self.assertTrue(os.path.isfile(xml_file))
        
        # Check XML content
        with open(xml_file, 'r', encoding='utf-8') as f:
            xml_content = f.read()
        self.assertIn('<dcvalue element="title" qualifier="none">A Great Thesis</dcvalue>', xml_content)
        self.assertIn('<dcvalue element="contributor" qualifier="author">Doe, John</dcvalue>', xml_content)
        
        # Check that content file was NOT copied, as per chc/full_record.php logic
        copied_file = os.path.join(output_dir, 'MyThesis.pdf')
        self.assertFalse(os.path.isfile(copied_file))


if __name__ == '__main__':
    unittest.main()
