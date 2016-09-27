<?php

namespace AppBundle\Utils\Cache;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Filesystem\Filesystem;

class FileSystemCache implements Cache {

    private $cacheDir;

    private $hits = 0;

    private $miss = 0;

    private $fileSystem;

    public function __construct($cacheDir)
    {
        $this->cacheDir     = $cacheDir . DIRECTORY_SEPARATOR . 'twig_cache' . DIRECTORY_SEPARATOR;
        $this->fileSystem   = new Filesystem();

        if(!$this->fileSystem->exists($this->cacheDir))
            $this->fileSystem->mkdir($this->cacheDir);
    }

    public function fetch($id)
    {
        if($this->fileSystem->exists($this->getCachePath($id))) {

            $this->hits++;
            return file_get_contents($this->getCachePath($id));
        }

        $this->miss++;
        return false;
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return $this->fileSystem->exists($this->getCachePath($id));
    }

    /**
     * Puts data into the cache.
     *
     * If a cache entry with the given id already exists, its data will be replaced.
     *
     * @param string $id The cache id.
     * @param mixed $data The cache entry/data.
     * @param int $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        if($this->fileSystem->exists($this->getCachePath($id)))
            $this->fileSystem->remove($this->getCachePath($id));

        file_put_contents($this->getCachePath($id), $data);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function delete($id)
    {
        $this->fileSystem->remove($this->getCachePath($id));
    }

    /**
     * Retrieves cached information from the data store.
     *
     * The server's statistics array has the following values:
     *
     * - <b>hits</b>
     * Number of keys that have been requested and found present.
     *
     * - <b>misses</b>
     * Number of items that have been requested and not found.
     *
     * - <b>uptime</b>
     * Time that the server is running.
     *
     * - <b>memory_usage</b>
     * Memory used by this server to store items.
     *
     * - <b>memory_available</b>
     * Memory allowed to use for storage.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    public function getStats()
    {
        return [
            'hits' => $this->hits,
            'misses' => $this->miss
        ];
    }

    private function getCachePath($id) {

        return $this->cacheDir . md5($id) . '.cache';
    }
}