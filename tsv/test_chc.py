import unittest
import os
import shutil
from chc.full_record import FullRecord
from base_record import BaseRecord, DublinCoreXML, SpecialChars

class TestChcFullRecord(unittest.TestCase):

    def setUp(self):
        """Set up a test environment."""
        self.proj_name = 'chc'
        # Create a dummy project structure if it doesn't exist
        os.makedirs(self.proj_name, exist_ok=True)
        
        # Create dummy config file from user-provided sample
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

        # Sample data as a tab-separated string, 27 columns
        self.sample_data = (
            "email@example.com\tJohn\tDoe\tA Great Thesis\tComputer Science\t"
            "AI\tMachine Learning\tNLP\tRobotics\tVision\t"
            "This is the abstract of the great thesis.\tPhD\tJane\tSmith\t"
            "CAS\tCS\t2025-05-15\t-\t-\t-\t"
            "restrict access\tAll rights reserved.\t1234-5678-9012-3456\tYes\t"
            "-\t-\t-\t-"
        )

    def test_full_record_creation_and_xml_generation(self):
        """Test creating a FullRecord and generating XML."""
        record = FullRecord()
        record.init(self.proj_name)
        record.get_configs()
        record.set_metadata(self.sample_data)

        # Test constructed fields
        self.assertEqual(record.title['val'], 'A Great Thesis')
        self.assertEqual(record.authors['val'], ['Doe, John'])
        self.assertEqual(record.advisors['val'], ['Smith, Jane'])
        self.assertEqual(len(record.subjects['val']), 5)
        self.assertEqual(record.subjects['val'][0], 'AI')
        self.assertEqual(record.embargo['val'], '9999')
        self.assertEqual(record.orcid['val'], '1234-5678-9012-3456')

        # Test XML generation
        xml_output = record.assemble_properties()
        
        self.assertIn('<dcvalue element="title" qualifier="none">A Great Thesis</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="contributor" qualifier="author">Doe, John</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="contributor" qualifier="advisor">Smith, Jane</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="subject" qualifier="none" language="en_US">AI</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="date" qualifier="issued">2025</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="publisher" qualifier="none">University of Oregon</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="description" qualifier="abstract" language="en_US">This is the abstract of the great thesis.</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="description" qualifier="embargo" language="en_US">9999</dcvalue>', xml_output)
        self.assertIn('<dcvalue element="identifier" qualifier="orcid">1234-5678-9012-3456</dcvalue>', xml_output)
        self.assertTrue(xml_output.startswith('<?xml version="1.0" ?><dublin_core schema="dc">'))
        self.assertTrue(xml_output.endswith('</dublin_core>'))

    def test_construct_dirname(self):
        """Test the directory name construction."""
        record = FullRecord()
        record.init(self.proj_name)
        record.get_configs()
        record.set_metadata(self.sample_data)
        dirname = record.construct_dirname()
        self.assertEqual(dirname, 'DoeJohn')

if __name__ == '__main__':
    unittest.main()
