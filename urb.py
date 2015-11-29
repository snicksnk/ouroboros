import os, re
import unittest, copy
from jinja2 import Template
from subprocess import call



data = {
    'source-path': '/var/www/urobo/template',
    'target-path': '/var/www/urobo/result',
    'module-name': 'Settings',
    'controllers':
    {
        'index': {
            'index'
        },
        'general': {
            'actions': {
                'index': "assa",
                'save': "ssasa"
            }
        },
        'blocking': {
            'actions': {
                'index': "",
                'search-user': "",
                'comment': "",
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
    'forms': {
        "Settings":{

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
            if len(subdir_relative_path) < 1:
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

        file = File(path=file_path)
        self.process_with_processors(file)

    def add_dir(self, dir_path):
        dir = Dir(path=dir_path)
        self.process_with_processors(dir)

    def process_with_processors(self, file_system_elm):
        for name, Processor in self.processors.iteritems():
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
        print 'create dir  ' + target_dir_path
        os.makedirs(target_dir_path)

    def render_template(self, file_data):
        template = Template(file_data.decode('utf-8'))
        return template.render(__config=self.config)

    def create_file(self, file):

        source_path = os.path.join(self.source_path, file.path)
        f = open(source_path, "r")
        file_data = f.read()

        path = self.replace_path_parts_by_replacers(file)
        target_file_path = os.path.join(self.target_path, path)

        rendered_data = self.render_template(file_data)

        result_file = open(target_file_path, "w+")

        print 'create file ' + target_file_path
        result_file.write(rendered_data)




class FileSystemElem(object):
    is_dir = False
    is_file = False

    def __init__(self, path):
        self._path = None
        self.path = path
        self.parent = None
        self._save_path = None

    @property
    def save_path(self):
        if not self._save_path:
            return self.path
        else:
            return self._save_path

    @save_path.setter
    def save_path(self, save_path):
        self._save_path = save_path

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
        #TODO fix it
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



class RepeatFile(FilesProcessors):
    name = 'RepeatFile'

    def get_name_parts(self, file_system_elem):
        p = re.compile('^__rpt_([^.]+)(.+)')
        result = p.findall(file_system_elem.name)
        if result:
            return result[0][0], result[0][1]

    def check_is_need(self, file_system_elem):
        result = self.get_name_parts(file_system_elem)
        if result:
            return True

    def process(self, file_system_elem):
        config_name, file_name_part = self.get_name_parts(file_system_elem)
        for name, data in self.processor.config[config_name].iteritems():
            self.processor.config['_'+config_name] = data
            new_file = copy.copy(file_system_elem)
            new_file.save_path = os.path.join(new_file.parent_dir, name+file_name_part)
            self.processor.create_file(new_file)


class TypicalFile(FilesProcessors):
    name = 'TypicalFile'

    def check_is_need(self, file_system_elem):
        if (file_system_elem.is_file):
            return True

    def process(self, file):
        #TODO fix it. diff from dir
        p = re.compile('^__rnm_([^.]+)(.+)')
        result = p.findall(file.name)
        if result:
            #diff from dir
            math_text = result[0][0]
            source_path = os.path.join(file.parent_dir, file.name)
            real_name = os.path.join(file.parent_dir, self.processor.config[math_text] + result[0][1])
            self.processor.add_dir_replace(source_path, real_name)

        self.processor.create_file(file)



print (call("rm -rf result/*", shell=True))

source = FilesSource(data)
processor = Processor(data)
processor.target_path = 'result'
processor.source_path = data['source-path']
processor.add_file_processor(RepeatFile)
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