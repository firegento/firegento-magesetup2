<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class CmsSubProcessor
 *
 * @package FireGento\MageSetup\Model\Setup\SubProcessor
 */
class CmsSubProcessor extends AbstractSubProcessor
{
    /**
     * @var \FireGento\MageSetup\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * CmsSubProcessor constructor.
     *
     * @param WriterInterface $configWriter
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param PageFactory $pageFactory
     * @param PageRepository $pageRepository
     * @param BlockFactory $blockFactory
     * @param BlockRepository $blockRepository
     */
    public function __construct(
        WriterInterface $configWriter,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        PageFactory $pageFactory,
        PageRepository $pageRepository,
        BlockFactory $blockFactory,
        BlockRepository $blockRepository
    ) {
        $this->moduleReader = $moduleReader;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        parent::__construct($configWriter);
    }

    /**
     * Process
     *
     * @param Config $config
     * @return void
     */
    public function process(Config $config)
    {
        $this->config = $config;

        $cmsPages = $config->getCmsPages();
        if ($cmsPages) {
            foreach ($cmsPages as $pageData) {
                $this->createCmsPage($pageData);
            }
        }

        $cmsBlocks = $config->getCmsBlocks();
        if ($cmsBlocks) {
            foreach ($cmsBlocks as $blockData) {
                $this->createCmsBlock($blockData);
            }
        }
    }

    /**
     * Create a cms page with the given data
     *
     * @param array $pageData
     */
    private function createCmsPage($pageData)
    {
        // Check if template filename exists
        $filename = $pageData['filename'];
        $template = $this->getTemplatePath('pages') . $filename;
        // phpcs:ignore
        if (!file_exists($template)) {
            return;
        }

        // Remove filename from data
        unset($pageData['filename']);

        // Fetch template content
        // phpcs:ignore
        $templateContent = @file_get_contents($template);

        $data = [
            'stores'    => [0],
            'is_active' => 1,
        ];

        if (preg_match('/<!--@title\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $data['title'] = $matches[1];
            $data['content_heading'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        if (preg_match('/<!--@identifier\s*((?:.)*?)\s*@-->/us', $templateContent, $matches)) {
            $data['identifier'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        if (isset($pageData['page_layout']) && !empty($pageData['page_layout'])) {
            $data['page_layout'] = $pageData['page_layout'];
        } else {
            $data['page_layout'] = '1column';
        }

        /**
         * Remove comment lines
         */
        $content = preg_replace('#\{\*.*\*\}#suU', '', $templateContent);
        $content = trim($content);
        $data['content'] = $content;

        $page = $this->pageFactory->create();
        $page = $page->load($data['identifier'], 'identifier');

        if ($page->getId()) {
            $data['page_id'] = $page->getId();
        }

        $page->addData($data);
        $this->pageRepository->save($page);

        if (isset($pageData['config_option'])) {
            $this->saveConfigValue($pageData['config_option'], $data['identifier'], 0);
        }
    }

    /**
     * Create a cms block with the given data
     *
     * @param array $blockData
     */
    private function createCmsBlock($blockData)
    {
        // Check if template filename exists
        $filename = $blockData['filename'];
        $template = $this->getTemplatePath('blocks') . $filename;
        // phpcs:ignore
        if (!file_exists($template)) {
            return;
        }

        // Remove filename from data
        unset($blockData['filename']);

        // Fetch template content
        // phpcs:ignore
        $templateContent = @file_get_contents($template);

        $data = [
            'stores'    => [0],
            'is_active' => 1,
        ];

        // Find title
        if (preg_match('/<!--@title\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $data['title'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        // Remove comment lines
        $content = preg_replace('#\{\*.*\*\}#suU', '', $templateContent);
        $content = trim($content);
        $data['content'] = $content;

        $block = $this->blockFactory->create();
        $block = $block->load($blockData['identifier'], 'identifier');

        if ($block->getId()) {
            $data['block_id'] = $block->getId();
        }

        $data['identifier'] = $blockData['identifier'];
        $block->addData($data);
        $this->blockRepository->save($block);
    }

    /**
     * Retrieve the template path for the given subdirectory
     *
     * @param string $subdirectory Sub-Directory
     * @return string
     */
    private function getTemplatePath($subdirectory)
    {
        $path = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_I18N_DIR,
            'FireGento_MageSetup'
        );

        $path .= DIRECTORY_SEPARATOR . 'template';
        $path .= DIRECTORY_SEPARATOR . $this->config->getCountry();
        $path .= DIRECTORY_SEPARATOR . $subdirectory;
        $path .= DIRECTORY_SEPARATOR;

        return $path;
    }
}
