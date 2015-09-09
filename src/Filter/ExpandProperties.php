<?php

/*
 *  $Id$
 *
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
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
*/
namespace Phing\Filter;

use Phing\Filter\BaseFilterReader;
use Phing\Filter\ChainableReaderInterface;
use Phing\Io\AbstractReader;
use Phing\Parser\ProjectConfigurator;
use Phing\Project;


/**
 * Expands Phing Properties, if any, in the data.
 * <p>
 * Example:<br>
 * <pre><expandproperties/></pre>
 * Or:
 * <pre><filterreader classname="phing.filters.ExpandProperties'/></pre>
 *
 * @author    Yannick Lecaillez <yl@seasonfive.com>
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Id$
 * @see       BaseFilterReader
 * @package   phing.filters
 */
class ExpandProperties extends BaseFilterReader implements ChainableReaderInterface
{
    protected $logLevel = Project::MSG_VERBOSE;

    /**
     * Set level of log messages generated (default = info)
     * @param string $level
     */
    public function setLevel($level)
    {
        switch ($level) {
            case "error":
                $this->logLevel = Project::MSG_ERR;
                break;
            case "warning":
                $this->logLevel = Project::MSG_WARN;
                break;
            case "info":
                $this->logLevel = Project::MSG_INFO;
                break;
            case "verbose":
                $this->logLevel = Project::MSG_VERBOSE;
                break;
            case "debug":
                $this->logLevel = Project::MSG_DEBUG;
                break;
        }
    }

    /**
     * Returns the filtered stream.
     * The original stream is first read in fully, and the Phing properties are expanded.
     *
     * @param null $len
     * @return mixed the filtered stream, or -1 if the end of the resulting stream has been reached.
     *
     * @exception IOException if the underlying stream throws an IOException
     * during reading
     */
    public function read($len = null)
    {

        $buffer = $this->in->read($len);

        if ($buffer === -1) {
            return -1;
        }

        return $this->getProject()->replaceProperties($buffer, $this->logLevel);
    }

    /**
     * Creates a new ExpandProperties filter using the passed in
     * Reader for instantiation.
     *
     * @param \Phing\Io\AbstractReader $reader A Reader object providing the underlying stream.
     *               Must not be <code>null</code>.
     *
     * @return ExpandProperties A new filter based on this configuration, but filtering
     *                the specified reader
     */
    public function chain(AbstractReader $reader)
    {
        $newFilter = new ExpandProperties($reader);
        $newFilter->setProject($this->getProject());

        return $newFilter;
    }
}