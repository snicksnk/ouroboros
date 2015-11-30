import os, re


class FileSystemElem(object):
    is_dir = False
    is_file = False

    def __init__(self, path):
        self._path = None
        self.parent = None
        self._save_path = None

        self.directive_name = None
        self.config_name = None
        self.file_name_part = None
        self.path = path

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
        self.get_name_parts()

    #TODO to name setter
    def get_name_parts(self):
        p = re.compile('__([a-z]+)_([^_]+)_(.{0,})')
        result = p.findall(self.name)
        if result:
            self.directive_name = result[0][0]
            self.config_name = result[0][1]
            self.file_name_part = result[0][2]

    def __unicode__(self):
        return self.path


class Dir(FileSystemElem):
    is_dir = True
    pass


class File(FileSystemElem):
    is_file = True
    pass