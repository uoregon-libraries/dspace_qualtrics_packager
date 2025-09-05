from base_record import BaseRecord

class FullRecord(BaseRecord):

    def __init__(self):
        super().__init__()
        self.advisors = {}
        self.orcid = {}
        self.embargo = {}

    def set_metadata(self, tsv_string: str) -> list[str]:
        arr = super().set_metadata(tsv_string)
        self.authors['val'] = self.construct_authors(arr)
        self.advisors['val'] = self.construct_advisors(arr)
        self.abstract['val'] = self.char_handler.clean(arr[self.abstract['ind']])
        self.description['val'] = ""
        # Check if orcid index exists and is within bounds
        if 'ind' in self.orcid and self.orcid['ind'] < len(arr):
            self.orcid['val'] = arr[self.orcid['ind']]
        else:
            self.orcid['val'] = ""
        
        # Check if rights index exists and is within bounds
        if 'ind' in self.rights and self.rights['ind'] < len(arr):
            self.rights['val'] = arr[self.rights['ind']]
        else:
            self.rights['val'] = ""

        self.embargo['val'] = self.construct_forever_embargo(arr)
        return arr

    def construct_dirname(self) -> str:
        if not self.authors.get('val'):
            return ""
        return self.authors['val'][0].replace(" ", "").replace("'", "").replace(",", "")

    def construct_authors(self, arr: list[str]) -> list[str]:
        authors = []
        if 'ind' in self.authors and isinstance(self.authors['ind'], list) and len(self.authors['ind']) == 2:
            start, end = self.authors['ind']
            for i in range(start, end + 1, 2):
                if i + 1 < len(arr):
                    last_name = self.char_handler.clean(arr[i+1])
                    first_name = self.char_handler.clean(arr[i])
                    authors.append(f"{last_name}, {first_name}")
        return authors

    def construct_advisors(self, arr: list[str]) -> list[str]:
        advisors = []
        if 'ind' in self.advisors and isinstance(self.advisors['ind'], list) and len(self.advisors['ind']) == 2:
            start, end = self.advisors['ind']
            for i in range(start, end + 1, 2):
                if i + 1 < len(arr):
                    last_name = self.char_handler.clean(arr[i+1])
                    first_name = self.char_handler.clean(arr[i])
                    advisors.append(f"{last_name}, {first_name}")
        return advisors

    def assemble_properties(self) -> str:
        string = self.dc_formatter.begin_xml()
        string += super().assemble_properties()
        
        for author in self.authors.get('val', []):
            string += self.dc_formatter.contributor(author)
        
        for advisor in self.advisors.get('val', []):
            string += self.dc_formatter.advisor(advisor)
            
        string += self.dc_formatter.embargo(self.embargo.get('val'))
        string += self.dc_formatter.orcid(self.orcid.get('val'))
        string += self.dc_formatter.end_xml()
        return string

    def construct_embargo(self, arr: list[str]) -> str:
        """
        Original logic for 2-year embargo.
        Note: The `construct_forever_embargo` is used in `set_metadata`.
        """
        if 'ind' in self.embargo and self.embargo['ind'] < len(arr):
            if 'restrict' in arr[self.embargo['ind']].lower():
                return self.add2Y()
        return ""

    def construct_forever_embargo(self, arr: list[str]) -> str:
        """
        If 'restrict' is in the embargo field, return '9999'.
        """
        if 'ind' in self.embargo and self.embargo['ind'] < len(arr):
            if 'restrict' in arr[self.embargo['ind']].lower():
                return "9999"
        return ""
