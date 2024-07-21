<?php

namespace App\Command;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:daily-report',
    description: 'Add a short description for your command',
)]
class DailyReportCommand extends Command
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates a daily Excel report and sends it by email.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'report 1');
        $sheet->setCellValue('B1', 'report 2');
        $sheet->setCellValue('A2', 'task completed 1');
        $sheet->setCellValue('B2', 'task completed 2');

        $writer = new Xlsx($spreadsheet);
        $tempFile = '/tmp/daily_report.xlsx';
        $writer->save($tempFile);

        $email = (new Email())
            ->from('sidorkinalex1931@outlook.com')
            ->to('alexsidorkin567@gmail.com')
            ->to('dev.lubinets@gmail.com')
            ->subject('Daily Report')
            ->text('Please find the daily report attached.')
            ->attachFromPath($tempFile);

        $this->mailer->send($email);

        $output->writeln('Daily report has been sent.');

        return Command::SUCCESS;
    }
}
