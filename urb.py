import os, re
import unittest
from jinja2 import Template
from subprocess import call



data = {
    'source-path': '/var/www/urobo/template',
    'target-path': '/var/www/urobo/result',
    'module-name': 'Video',
    'controllers':
    {
        'index':{
            'actions':{
                'index':"assa",
                'get':"ssasa"
            }
        }
    }

}


class FilesSource:
    def __init__(self, config):
        self.rootDir = config['source-path']
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
            if (len(subdir_relative_path)<1):
                continue

            self.processor.add_dir(subdir_relative_path)
            for one_file in files:
                file_path = os.path.join(subdir, one_file)
                file_relative_path = self.get_relative_path(file_path, self.rootDir)
                self.processor.add_file(file_relative_path)

    def get_relative_path(self, path, source_path):
        return path[len(source_path)+1:]


class Processor:
    def __init__(self, config):
        self.config = config
        self.source_path = None
        self.target_path = None
        self.processors = {}
        self.current_dir = Dir(path='')
        self.replaces = {}


    def add_file_processor(self, file_processor):
        self.processors[file_processor.name] = file_processor
        pass

    def add_file(self, file_path):
        file = File(path=file_path, source_path=self.source_path, target_path=self.source_path, current_path = '')


    def add_dir(self, dir_path):
        dir = Dir(path=dir_path, source_path=self.source_path, target_path=self.source_path, current_path = '')
        for name, Processor in self.processors.iteritems():
            processor = Processor(self)
            if (processor.check_is_need(dir)):
                processor.process(dir)

    def add_dir_replace(self, source_name, real_name):
        self.replaces[source_name] = real_name

    def create_dir(self, dir):
        path = dir.path
        for source_name, real_name in self.replaces.iteritems():
            path = re.sub(re.compile(source_name), real_name, path)

        print 'create dir  ' + os.path.join(self.target_path, path)



class FileSystemElem(object):
    is_dir = False
    is_file = False

    def __init__(self, path, source_path = '', target_path = '', current_path = ''):
        self._path = None
        self.path = path
        self.parent = None
        self.target_path = source_path
        self.source_path = target_path
        self.current_path = current_path


    @property
    def path(self):
        return os.path.join(self.parent_dir, self.name)

    @path.setter
    def path(self, path):
        self.parent_dir = os.path.dirname(path)
        self.name = os.path.basename(path)



    def __unicode__(self):
        return self.path


class Dir(FileSystemElem):
    is_dir = True
    pass


class File(FileSystemElem):
    is_file = True
    pass


class FilesProcessors:
    def __init__(self, processor):
        self.processor = processor


class Config():
    def __init__(self, config):
        self.config = config



class DirProcessor(FilesProcessors):
    name = 'DirProcessor'

    def check_is_need(self, file_system_elem):
        if file_system_elem.is_dir:
            return True

    def process(self, dir):
        p = re.compile('^__rnm_(.+)')
        result = p.findall(dir.name)
        if result:
            math_text = result[0]
            source_path = os.path.join(dir.parent_dir, dir.name)
            real_name = os.path.join(dir.parent_dir, self.processor.config[math_text])
            self.processor.add_dir_replace(source_path, real_name)
        else:
            pass

        self.processor.create_dir(dir)
        self.processor.current_dir = dir



class TypicalFile(FilesProcessors):
    name = 'TypicalFile'

    def check_is_need(self, file_system_elem):
        if (file_system_elem.is_file):
            print('is file')




source = FilesSource(data)
processor = Processor(data)
processor.target_path = 'result'
processor.source_path = data['source-path']
processor.add_file_processor(TypicalFile)
processor.add_file_processor(DirProcessor)

source.processor = processor
source.get_files()


print (call("rm -rf result/*", shell=True))

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