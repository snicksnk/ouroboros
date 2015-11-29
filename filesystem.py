import os


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