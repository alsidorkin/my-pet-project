<?php
namespace App\cron\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use phpseclib3\Net\SSH2;
use phpseclib3\Exception\UnableToConnectException;

class CronController extends AbstractController
{
    private $ssh;
    private $username;
    private $password;

    public function __construct()
    {
        $host = '127.0.0.1';
        $port = 22;
        $this->username = '****';
        $this->password = '*****';
        $this->ssh = new SSH2($host, $port);
        if (!$this->ssh->login($this->username, $this->password)) {
            throw new UnableToConnectException('Unable to connect to the remote server with provided credentials.');
        }
    }
   
    #[Route('/cron', name: 'app_cron')]
    public function index(): Response
    {
        $output = $this->ssh->exec('crontab -l');
        $cronJobs = explode(PHP_EOL, trim($output));

        return $this->render('cron/index.html.twig', [
            'cronJobs' => $cronJobs,
        ]);
    }

}
