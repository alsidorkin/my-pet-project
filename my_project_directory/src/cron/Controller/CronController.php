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
        $this->username = '***';
        $this->password = '****';
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

     #[Route('/cron/add', name: 'cron_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
            $minute = $request->request->get('minute');
            $hour = $request->request->get('hour');
            $day = $request->request->get('day');
            $month = $request->request->get('month');
            $weekday = $request->request->get('weekday');
            $command = $request->request->get('command');

    if ($minute === '' && $hour === '' && $day === '' && $month === '' && $weekday === '' && $command === '') {
        $this->addFlash('error', 'All fields must be filled.');
        return $this->redirectToRoute('app_cron');
    }
    $cronJob = "$minute $hour $day $month $weekday $command >/dev/null 2>&1";
    $output = $this->ssh->exec('crontab -l');
    $output .= $cronJob . PHP_EOL;
    file_put_contents('/tmp/crontab.txt', $output);
    $this->ssh->exec('echo "' . addslashes($output) . '" > /tmp/crontab.txt');
    $this->ssh->exec('crontab /tmp/crontab.txt');
    $this->addFlash('success', 'Cron job added successfully.');
    return $this->redirectToRoute('app_cron');
    }



    #[Route('/cron/remove', name: 'cron_remove', methods: ['POST'])]
    public function remove(Request $request): Response
    {
        $cronJob = $request->request->get('cronJob');

        if (empty($cronJob)) {
            $this->addFlash('error', 'Cron job field cannot be empty.');
            return $this->redirectToRoute('app_cron');
        }

        $output = $this->ssh->exec('crontab -l');
        $cronArray = explode(PHP_EOL, $output);
        $cronArray = array_filter($cronArray, function($line) use ($cronJob) {
            return strpos($line, $cronJob) === false;
        });

        file_put_contents('/tmp/crontab.txt', implode(PHP_EOL, $cronArray) . PHP_EOL);
        $this->ssh->exec('echo "' . addslashes(implode(PHP_EOL, $cronArray)) . '" > /tmp/crontab.txt');
        $this->ssh->exec('crontab /tmp/crontab.txt');
        $this->addFlash('success', 'Cron job removed successfully.');

        return $this->redirectToRoute('app_cron');
    }

}
