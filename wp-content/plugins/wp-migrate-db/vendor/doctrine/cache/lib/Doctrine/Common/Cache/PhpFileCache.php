<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace DeliciousBrains\WPMDB\Container\Doctrine\Common\Cache;

/**
 * Php file cache driver.
 *
 * @since  2.3
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PhpFileCache extends FileCache
{
    const EXTENSION = '.doctrinecache.php';
    /**
     * {@inheritdoc}
     */
    public function __construct($directory, $extension = self::EXTENSION)
    {
        parent::__construct($directory, $extension);
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $filename = $this->getFilename($id);
        if (!\is_file($filename)) {
            return \false;
        }
        $value = (include $filename);
        if ($value['lifetime'] !== 0 && $value['lifetime'] < \time()) {
            return \false;
        }
        return $value['data'];
    }
    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $filename = $this->getFilename($id);
        if (!\is_file($filename)) {
            return \false;
        }
        if (!\is_readable($filename)) {
            return \false;
        }
        $value = (include $filename);
        return $value['lifetime'] === 0 || $value['lifetime'] > \time();
    }
    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            $lifeTime = \time() + $lifeTime;
        }
        if (\is_object($data) && !\method_exists($data, '__set_state')) {
            throw new \InvalidArgumentException("Invalid argument given, PhpFileCache only allows objects that implement __set_state() " . "and fully support var_export(). You can use the FilesystemCache to save arbitrary object " . "graphs using serialize()/deserialize().");
        }
        $filename = $this->getFilename($id);
        $value = array('lifetime' => $lifeTime, 'data' => $data);
        $value = \var_export($value, \true);
        $code = \sprintf('<?php return %s;', $value);
        return $this->writeFile($filename, $code);
    }
}
