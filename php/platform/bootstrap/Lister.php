<?php
namespace NGS;

class Lister
{
    private $path;
    private $pattern;

    public $sizes;
    public $hashes;
    public $bodies;

    public $dirs;
    public $aggHash;

    public function __construct($path, $pattern = '.*')
    {
        $this->path = $path;
        $this->pattern = $pattern;

        $this->sizes = array();
        $this->hashes = array();
        $this->bodies = array();

        $this->readFiles();
        $this->aggHash = self::hashHashes($this->hashes);
    }

    private function readFiles($rel = '.')
    {
        $dir = $this->path.$rel;
        $this->dirs[$rel] = $dir;

        $parent = opendir($dir);
        if ($parent === false) {
            throw new \Exception('Could not read the DSL folder: '.$dir);
        }

        while (true) {
            $filename = readdir($parent);
            if ($filename === false) {
                break;
            }

            if ($filename === '.' || $filename === '..') {
                continue;
            }

            $relFile = $rel.'/'.$filename;
            $path = $this->path.$relFile;

            if (is_dir($path)) {
                $this->readFiles($relFile);
            }
            elseif (is_file($path) && preg_match('/^'.$this->pattern.'$/u', $path)) {
                $relFile = mb_substr($relFile, 2);
                $body = file_get_contents($path);
                $this->sizes[$relFile] = strlen($body);
                $this->hashes[$relFile] = sha1($body, true);
                $this->bodies[$relFile] = $body;
            }
        }

        closedir($parent);
    }

    private static function hashHashes($hashes)
    {
        sort($hashes);
        $agg = implode('', $hashes);
        return sha1($agg, true);
    }
}
