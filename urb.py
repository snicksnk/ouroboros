import os, re
import unittest, copy
from jinja2 import Template
from subprocess import call
from processors import RepeatFile, TypicalFile, DirProcessor, RenameFile
from filesystem import Dir, File

data = {
    'source_path': '/var/www/urobo/template',
    'target_path': '/var/www/urobo/result',
    'module_name': 'Settings',
    'controller':
    {
        'Index': {
            'index',
	    'User'
        },
        'General': {
            'actions': {
                'index': "assa",
                'save': "ssasa"
            }
        },
        'Blocking': {
            'actions': {
                'Index': "",
                'Search_user': "",
                'omment': "",
                'message': ""
            }
        }
    },
    'service': {
        'Settings': ""
    },
    'repository': {
        'Settings': ""
    },
    'form': {
        "Settings": {
            "rows": [
                'email', 'password', 'azaza'
            ],
            "filter": 'Settings'
        }
    },
    'filter': {
        "Settings": {
            'rows': [
                'email', 'password', 'azaza'
            ]
        }
    }

}


class FilesSource:
    def __init__(self, config):
        self.rootDir = config['source_path']
        self.__processor = None

    @property
    def processor(self, processor):
        self.__processor = processor

    @processor.setter
    def processor(self, processor):
        self.__processor = processor

    def get_files(self):
        for subdir, dirs, files in os.walk(self.rootDir):
            subdir_relative_path = self.get_relative_path(subdir, self.rootDir);
            if len(subdir_relative_path) < 1:
                continue

            self.processor.add_dir(subdir_relative_path)
            for one_file in files:
                file_path = os.path.join(subdir, one_file)
                file_relative_path = self.get_relative_path(file_path, self.rootDir)
                self.processor.add_file(file_relative_path)

    def get_relative_path(self, path, source_path):
        return path[len(source_path)+1:]


class BornState:
    def __init__(self, config):
        self.config = config
        self.source_path = None
        self.target_path = None
        self.processors = []
        self.current_dir = Dir(path='')
        self.replaces = {}

    def add_file_processor(self, file_processor):
        self.processors.append(file_processor)
        pass

    def add_file(self, file_path):
        file = File(path=file_path)
        self.process_with_processors(file)

    def add_dir(self, dir_path):
        dir = Dir(path=dir_path)
        self.process_with_processors(dir)

    def process_with_processors(self, file_system_elm):
        for Processor in self.processors:
            processor = Processor(self)
            if processor.check_is_need(file_system_elm):
                processor.process(file_system_elm)


    def add_dir_replace(self, source_name, real_name):
        self.replaces[source_name] = real_name

    def replace_path_parts_by_replacers(self, file_system_elm):
        path = file_system_elm.save_path
        for source_name, real_name in self.replaces.iteritems():
            path = re.sub(re.compile(source_name), real_name, path)

        return path

    def create_dir(self, dir):
        path = self.replace_path_parts_by_replacers(dir)
        target_dir_path = os.path.join(self.target_path, path)
        print 'create dir %s' % target_dir_path
        os.makedirs(target_dir_path)

    def render_template(self, file_data):
        template = Template(file_data.decode('utf-8'))
        return template.render(__config=self.config, **self.config)

    def create_file(self, file):

        source_path = os.path.join(self.source_path, file.path)
        f = open(source_path, "r")
        file_data = f.read()

        path = self.replace_path_parts_by_replacers(file)
        target_file_path = os.path.join(self.target_path, path)

        rendered_data = self.render_template(file_data)

        result_file = open(target_file_path, "w+")

        print 'create file %s' % target_file_path
        result_file.write(rendered_data)



print (call("rm -rf result/*", shell=True))

source = FilesSource(data)
processor = BornState(data)
processor.target_path = 'result'
processor.source_path = data['source_path']
processor.add_file_processor(RepeatFile)
processor.add_file_processor(RenameFile)
processor.add_file_processor(TypicalFile)
processor.add_file_processor(DirProcessor)

source.processor = processor
source.get_files()



class TestStringMethods(unittest.TestCase):

    def test_upper(self):
        self.assertEqual('foo'.upper(), 'FOO')

    def test_isupper(self):
        self.assertTrue('FOO'.isupper())
        self.assertFalse('Foo'.isupper())

    def test_split(self):
        s = 'hello world'
        self.assertEqual(s.split(), ['hello', 'world'])
        # check that s.split fails when the separator is not a string
        with self.assertRaises(TypeError):
            s.split(2)

#if __name__ == '__main__':
    #unittest.main()