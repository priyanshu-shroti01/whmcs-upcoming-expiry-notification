<?php
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Admin dashboard widget - Upcoming Expiring Services
 */
add_hook('AdminHomeWidgets', 1, function() {
    return new class {
        public $title = 'Upcoming Expiring Services';
        public $description = 'Shows services expiring in the next 7 days with client details.';
        public $weight = 10;
        public $columns = 1;

        public function getData()
        {
            $today = date('Y-m-d');
            $nextWeek = date('Y-m-d', strtotime('+7 days'));

            return Capsule::table('tblhosting')
                ->join('tblclients', 'tblclients.id', '=', 'tblhosting.userid')
                ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
                ->select(
                    'tblclients.id as client_id',
                    'tblclients.firstname',
                    'tblclients.lastname',
                    'tblclients.email',
                    'tblproducts.name as product_name',
                    'tblhosting.domain',
                    'tblhosting.nextduedate'
                )
                ->whereBetween('tblhosting.nextduedate', [$today, $nextWeek])
                ->where('tblhosting.domainstatus', 'Active')
                ->orderBy('tblhosting.nextduedate', 'asc') // ASCENDING by expiry date
                ->get();
        }

        public function render()
        {
            $services = $this->getData();

            if ($services->isEmpty()) {
                return '<p>No services expiring in the next 7 days.</p>';
            }

            $html = '<table class="table table-condensed table-hover">';
            $html .= '<thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Expiry Date</th>
                        </tr>
                      </thead><tbody>';

            foreach ($services as $service) {
                $clientLink = "<a href='clientssummary.php?userid={$service->client_id}'>" .
                              "{$service->firstname} {$service->lastname}</a>";
                $emailLink = "<a href='mailto:{$service->email}'>{$service->email}</a>";
                $serviceName = $service->product_name . 
                               ($service->domain ? " ({$service->domain})" : '');

                $html .= "<tr>
                            <td>{$clientLink}</td>
                            <td>{$emailLink}</td>
                            <td>{$serviceName}</td>
                            <td>{$service->nextduedate}</td>
                          </tr>";
            }

            $html .= '</tbody></table>';
            return $html;
        }
    };
});
