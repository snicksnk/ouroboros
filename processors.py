import os, re, copy


class FilesProcessors:
    def __init__(self, processor):
        self.processor = processor

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



    def check_is_need(self, file_system_elem):
        if file_system_elem.directive_name == 'rpt':
            return True

    def process(self, file_system_elem):
        for name, data in self.processor.config[file_system_elem.config_name].iteritems():
            self.processor.config['_' + file_system_elem.config_name] = data
            self.processor.config['_' + file_system_elem.config_name + '_name'] = name
            new_file = copy.copy(file_system_elem)
            new_file.save_path = os.path.join(new_file.parent_dir, name + file_system_elem.file_name_part)
            self.processor.create_file(new_file)


class TypicalFile(FilesProcessors):
    name = 'TypicalFile'

    def check_is_need(self, file_system_elem):
        if not file_system_elem.directive_name and file_system_elem.is_file:
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

