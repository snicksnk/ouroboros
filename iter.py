import os, re
import unittest
from jinja2 import Template

rootdir = 'template'

data = {
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

class TypicallFile:
    def getName(self):
        return 'TypicallFile'
    def checkIsNeed(self, file):
        return True
    def process(self, file, fileData):
        template = Template(fileData.decode('utf-8'))
        return template.render(__config=self.config, __filePath = file)
    def nextProcessor(self):
        pass

class RenameFile:
    def getName(self):
        return 'RenameFile'
    def checkIsNeed(self, file):
        valid = re.compile(r"__rnm_")
        print '!!!!' + file.name, valid.match(file.name)
        return valid.match(file.name)
    def process(self, file, fileData):
        print '!!!!!!' + (file.name)
    def nextProcessor(self):
        return 'TypicallFile'

class File:
    def __init__(self, name):
        self.name = name
        self.dir = os.path.dirname(name)
        self.content = None
    def getTargetPath(self, basePath, targetPath):
        resultPath = os.path.join(targetPath, self.name[len(basePath)+1:])
        return resultPath
    def createDirIfNotExists(self, basePath, targetPath):
        resultPath = self.getTargetPath(basePath, targetPath)
        if not os.path.exists(os.path.dirname(resultPath)):
            os.makedirs(os.path.dirname(resultPath))
	def save(File):
		fTarget = open(file.getTargetPath(self.basePath, self.targetPath), "w+")
		fTarget.write(processedFileData)





class Processor:
    def __init__(self, config, basePath, targetPath):
        self.files = []
        self.processedFiles = []
        self.processors = {}
        self.basePath = basePath
        self.targetPath = targetPath
        self.config = config
    def addFile(self, filePath):
        print filePath
	def addDir(self, dirPath):
		print dirPath
    def addProcessor(self, processor):
        processor.config = self.config
        self.processors[processor.getName()] = (processor)
    def process(self):
        for fileName in self.files:

            file = File(fileName)

            f = open(file.name, "r")
            fileData = f.read()

            #Process result path and create
            file.createDirIfNotExists(self.basePath, self.targetPath)
            processedFileData = False
            for name, processor in self.processors.iteritems():
                if (processor.checkIsNeed(file)):
                    processedFileData = processor.process(file, fileData)
                    #print processedFileData

            if (processedFileData):
                fTarget = open(file.getTargetPath(self.basePath, self.targetPath), "w+")
                fTarget.write(processedFileData)



class FilesSource:
    def __init__(self, rootDir):
        self.rootDir = rootDir
    def addProcessor(self, processor):
        self.processor = processor
    def getFiles(self):
        for subdir, dirs, files in os.walk(self.rootDir):
			self.processor.addDir(filePath)
            for file in files:
                filePath = os.path.join(subdir, file)
                self.processor.addFile(filePath)





filesProcessor = Processor(data, "template", "result")
fileSource = FilesSource(rootdir)
fileSource.addProcessor(filesProcessor)
fileSource.getFiles()


#filesProcessor.addProcessor(RenameFile())
filesProcessor.addProcessor(TypicallFile())

filesProcessor.process()
