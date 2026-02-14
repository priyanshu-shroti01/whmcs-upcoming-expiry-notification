<?php
namespace WHMCS\Module\Widget;

use WHMCS\Module\AbstractWidget;
use WHMCS\Database\Capsule;

class UpcomingExpiries extends AbstractWidget
{
    protected $title = 'Upcoming Expirations • Shroti Host';
    protected $description = 'Hosting & Domain services expiring soon (next 7 days).';
    protected $weight = 10;
    protected $columns = 1;

    public function getData()
    {
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));

        $hostings = Capsule::table('tblhosting')
            ->join('tblclients', 'tblclients.id', '=', 'tblhosting.userid')
            ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
            ->select(
                Capsule::raw("'Hosting' as type"),
                'tblclients.id as client_id',
                'tblclients.firstname',
                'tblclients.lastname',
                'tblclients.email',
                'tblproducts.name as service_name',
                'tblhosting.domain',
                'tblhosting.nextduedate as expiry_date'
            )
            ->whereBetween('tblhosting.nextduedate', [$today, $nextWeek])
            ->where('tblhosting.domainstatus', 'Active');

        $domains = Capsule::table('tbldomains')
            ->join('tblclients', 'tblclients.id', '=', 'tbldomains.userid')
            ->select(
                Capsule::raw("'Domain' as type"),
                'tblclients.id as client_id',
                'tblclients.firstname',
                'tblclients.lastname',
                'tblclients.email',
                Capsule::raw("CONCAT(tbldomains.domain, ' (Domain)') as service_name"),
                Capsule::raw("'' as domain"),
                'tbldomains.expirydate as expiry_date'
            )
            ->whereBetween('tbldomains.expirydate', [$today, $nextWeek])
            ->where('tbldomains.status', 'Active');

        return $hostings->unionAll($domains)
                        ->orderBy('expiry_date', 'asc')
                        ->get();
    }

    public function generateOutput($data)
    {
        if ($data->isEmpty()) {
            return '<p>No upcoming expirations in the next 7 days.</p>';
        }

        $html = '<table class="table table-hover" style="background:#fff; border:1px solid #ddd;">';
        $html .= '<thead style="background:#0055CC; color:#fff;"><tr>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Expiry</th>
                  </tr></thead><tbody>';

        foreach ($data as $row) {
            $type = htmlspecialchars($row->type);
            $clientLink = "<a href='clientssummary.php?userid={$row->client_id}' style='color:#28A745;'>" .
                          htmlspecialchars($row->firstname . ' ' . $row->lastname) . '</a>';
            $emailLink = "<a href='mailto:{$row->email}' style='color:#0055CC;'>" .
                         htmlspecialchars($row->email) . '</a>';
            $service = htmlspecialchars($row->service_name);
            $expiry = htmlspecialchars($row->expiry_date);

            $html .= "<tr>
                        <td>{$type}</td>
                        <td>{$clientLink}</td>
                        <td>{$emailLink}</td>
                        <td>{$service}</td>
                        <td>{$expiry}</td>
                      </tr>";
        }

        $html .= '</tbody></table>';
        return $html;
    }

    public function render($forceRefresh = false)
    {
        $out = '<div style="font-family: Arial, sans-serif;">';
        $out .= '<img src="https://shrotihost.in/wp-content/uploads/2026/01/Shroti-Host-logo-dark.png" alt="Shroti Host" style="height:24px; margin-bottom:8px;">';
        $out .= $this->generateOutput($this->getData());
        $out .= '</div>';
        return $out;
    }
}
