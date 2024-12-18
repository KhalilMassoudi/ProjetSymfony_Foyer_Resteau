<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class GenerateStaticSiteCommand extends Command
{
    protected static $defaultName = 'app:generate-static-site';
    private HttpKernelInterface $httpKernel;

    public function __construct(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate static HTML files from Symfony routes.')
            ->setHelp('This command pre-renders your Symfony routes into static HTML files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    // Define routes to render
    $routes = [
        '/' => 'fronttemplates/basefront.html.twig',
        '/register' => 'backtemplates/app_register.html.twig',
        '/profile' => 'fronttemplates/profile.html.twig',
    ];

    // Output directory for static files
    $outputDir = __DIR__ . '/../../static';
    $frontTemplatesDir = $outputDir . '/fronttemplates'; // Directory where you'll store static HTML files

    // Check and create directories if they don't exist
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    if (!is_dir($frontTemplatesDir)) {
        mkdir($frontTemplatesDir, 0777, true);  // Ensure fronttemplates directory exists
    }

    // Loop through routes and generate static files
    foreach ($routes as $route => $template) {
        // Create a Request object for the route
        $request = Request::create($route);

        // Handle the request and get the Response
        $response = $this->httpKernel->handle($request);
        $html = $response->getContent();

        // Construct the output file path in the static directory
        $filePath = $frontTemplatesDir . '/' . basename($template, '.twig') . '.html'; // Use the template name for the output file

        // Write the rendered HTML to a static file
        file_put_contents($filePath, $html);

        $output->writeln("Rendered $route to $filePath");
    }

    $output->writeln("Static files generated in: $outputDir");
    return Command::SUCCESS;
}

}
