<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Events\Notify;
use App\Models\File;
use DateTime;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use GuzzleHttp\Client as ApiClient;

abstract class Controller {

    public function file_info ( $file ) {

        $file_name = $file->getClientOriginalName();
        $file_type = $file->getMimeType();
        $ext = $file->extension();
        $size = $file->getSize();

        if ($size >= 1024 && $size < 1048576) $size = round($size / 1024, 1) . ' KB';
        else if ($size >= 1048576 && $size < 1073741824) $size = round($size / 1048576, 1) . ' MB';
        else if ($size >= 1073741824) $size = round($size / 1073741824, 1) . ' GB';
        else $size = $size ?? 0 . ' Byte';

        $name = array_values(array_filter(explode('.', $file_name), function($item){ return $item; }));
        if ( count($name) > 1 ) $name = implode('.', array_slice($name, 0, -1));
        else if ( count($name) == 1 ) $name = $name[0];
        else $name = $file_name;

        $type = explode('/', $file_type)[0] ?? 'file';
        if ( $type != 'image' && $type != 'video' ) $type = 'file';

        return ['name' => $name, 'size' => $size, 'type' => $type, 'ext' => $ext];

    }
    public function upload_file ( $file, $dir ) {

        if ( !$file ) return null;
        $info = $this->file_info($file);
        $info['url'] = $file->store($dir . '/' . date('Y') . '/' . date('m') . '/' . date('d'));
        return $info;

    }
    public function delete_file ( $file ) {

        if ( !$file ) return false;
        if ( !Storage::exists($file) ) return false;
        Storage::delete($file);
        return true;

    }
    public function upload_files ( $files, $dir, $id ) {

        foreach ( $files as $file ) {

            if ( !$file ) continue;
            $data = $this->upload_file($file, $dir);
            $data['table'] = $dir;
            $data['column'] = $id;
            File::create($data);

        }

        return true;

    }
    public function delete_files ( $ids, $dir ) {

        foreach ( $ids as $id ) {

            $file = File::where('id', $id)->where('table', $dir)->first();
            $this->delete_file($file?->url);
            $file?->forceDelete();

        }

        return true;

    }
    public function slug ( $name ) {

        return strtolower(trim(preg_replace('/\./', '', preg_replace('/\s/', '-', $name))));

    }
    public function string ( $value ) {

        $values = ['', 'null', 'undefined'];
        if ( in_array(strval($value), $values) ) return null;
        return $value ?? null;

    }
    public function bool ( $value ) {

        $value = trim(strtolower($value));
        $values = ['true', '1', 't', 'yes', 'y', 'ya', 'yep', 'ok', 'on', 'done', 'always'];
        if ( in_array($value, $values) ) return true;
        return false;

    }
    public function integer ( $value ) {

        return (int)$value;

    }
    public function float ( $value, $decimal=2 ) {

        return round((float)$value, $decimal);

    }
    public function parse ( $value ) {

        $value = json_decode($value) ?? [];
        if ( getType($value) != 'array' ) $value = [$value];
        return $value;

    }
    public function key_value ( $string ) {

        $key = ''; $value = '';
        $query = preg_replace("/=+/", "=", $string);
        $query = explode("=", $query);
        if ( count($query) > 1 ) { $key = $query[0]; $value = $query[1]; }

        if ( $key ) return ['key' => $key,'value'=> $value ];
        else return ['key' => '','value'=> ''];

    }
    public function random_key () {

        $key = '';
        for ( $i = 1; $i < 10; $i++ ) {
            $key .= rand(0, 9);
            if ( $i % 3 === 0 && $i != 9 ) $key .= '-';
        }
        return $key;

    }
    public function failed ( $response=[] ) {

        return response()->json(['status' => false, 'errors' => $response]);

    }
    public function success ( $response=[] ) {

        return response()->json(['status' => true] + $response);

    }
    public function user () {

        return Auth::guard('sanctum')->user();

    }
    public function date () {

        return date('Y-m-d H:i:s');

    }
    public function get_location ( $address ) {

        $client = new ApiClient();

        try{

            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query' => ['q' => $address, 'format' => 'json', 'limit' => 1, 'accept-language' => 'ar'],
                'headers' => ['User-Agent' => 'microtech/1.0 (microtech@gmail.com)'],
            ]);
            $data = json_decode($response->getBody(), true);
            if ( !empty($data) ) return ['longitude' => $data[0]['lon'], 'latitude' => $data[0]['lat']];

        } catch (\Exception $e) {}

        return ['longitude' => '46.6753', 'latitude' => '24.7136'];

    }
    public function send_sms ( $phone, $message ) {

        $api_key = "7019ef4b";
        $sec_key = "q8SVI6913VMfCyR5";
        $company = "Microtech";
        $basic  = new Basic($api_key, $sec_key);
        $client = new Client($basic);
        $response = $client->sms()->send(new SMS($phone, $company, $message));

        return true;

    }
    public function send_whatsapp ( $phone, $message ) {

        $instance_id = "instance89613";
        $api_token = "a600sm5p56zzpfaw";
        $client = new ApiClient();

        $response = $client->post("https://api.ultramsg.com/{$instance_id}/messages/chat", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$api_token}",
            ],
            'json' => [
                'token' => $api_token,
                'to' => $phone,
                'body' => $message,
            ]
        ]);

        return true;

    }
    public function user_table ( $req ) {

        $location = $this->get_location("{$req->street}, {$req->city}, {$req->country}");

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'age' => $this->float($req->age),
            'salary' => $this->float($req->salary),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => "{$req->street}, {$req->city}, {$req->country}",
            'longitude' => $location['longitude'],
            'latitude' => $location['latitude'],
            'currency' => $this->string($req->currency),
            'notes' => $this->string($req->notes),
            'allow_categories' => $this->bool($req->allow_categories),
            'allow_products' => $this->bool($req->allow_products),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow_blogs' => $this->bool($req->allow_blogs),
            'allow_comments' => $this->bool($req->allow_comments),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow_reports' => $this->bool($req->allow_reports),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'allow_contacts' => $this->bool($req->allow_contacts),
            'allow_clients' => $this->bool($req->allow_clients),
            'allow_vendors' => $this->bool($req->allow_vendors),
            'allow_statistics' => $this->bool($req->allow_statistics),
            'allow_messages' => $this->bool($req->allow_messages),
            'allow_mails' => $this->bool($req->allow_mails),
            'allow_login' => $this->bool($req->allow_login),
            'allow_likes' => $this->bool($req->allow_likes),
            'allow_dislikes' => $this->bool($req->allow_dislikes),
            'supervisor' => $this->bool($req->supervisor),
            'active' => $this->bool($req->active),
        ];

        return $data;

    }
    public function report ( $req, $table='', $column=0, $process='', $creator='', $data=[] ) {

        $inputs = [
            'table' => $table,
            'column' => $column,
            'process' => $process,
            'ip' => $req->ip(),
            'agent' => $req->userAgent(),
        ];

        if ( $creator === 'admin' ) $inputs['admin_id'] = $this->user()?->id ?? 0;
        if ( $creator === 'vendor' ) $inputs['vendor_id'] = $this->user()?->id ?? 0;
        if ( $creator === 'client' ) $inputs['client_id'] = $this->user()?->id ?? 0;

        $report = Report::create($inputs + $data);
        $report = ReportResource::make( $report );

        event(new Notify($this->user()?->id, $report));

    }
    public function paginate ( $table, $request ) {
        
        $page = $request->page;
        $limit = $request->limit;
        $search = $request->search;
        $filters = $request->filters;
        $filter = $request->filter;
        $count = $table->count();
        $name = $table->first()->getTable();
        $items = $table->orderBy("id", $filter === 'oldest' ? 'asc' : 'desc');

        if ( $search ) {

            $columns = Schema::getColumnListing($name);
            $search = trim($search);

            $items = $items->where('id', $search)
                ->orWhere('id', str_replace('=', '', $search))
                ->orWhere('id', str_replace('-', '', $search))
                ->orWhere(function ($subQuery) use ($search, $columns) {
                    foreach ($columns as $column) {
                        $subQuery->orWhere($column, 'like', "%{$search}%");
                    }
                });

            $count = $items->count();

        }
        if ( $filters ) {
            
            $filters = $this->parse($request->filters);
            $filters = count($filters) ? $filters[0] : $filters;

            foreach ( $filters as $key => $value ) {
                $items = $items->where($key, $value);
                $count = $items->count();
            }

        }
        if ( $filter && $filter !== 'oldest' && $filter !== 'newest' ) {

            if ( $name === 'orders' ) $items = $items->where('status', $filter);

        }
        if ( $limit ) {

            $items = $items->forPage($page ?? 1, $limit);

        }

        return ['items' => $items->get(), 'total' => $count];

    }
    public function series ( $items, $rng, $duration ) {

        $list = array_fill(0, $rng, 0);

        foreach( $items as $item ) {

            $date1 = new DateTime( $item->created_at );
            $date2 = new DateTime();
            $diff = $date2->getTimestamp() - $date1->getTimestamp();

            for( $ch=1; $ch <= $rng; $ch++ ) {

                if ( $duration * $ch >= $diff && $diff > $duration * ($ch-1) ) {

                    $list[$ch-1]++;
                    break;

                }

            }

        }

        return array_reverse($list);

    }
    public function charts ( $table ) {

        $total = count($table->get());
        $year_items = $table->whereBetween('created_at', [now()->subYears(7), now()])->get();
        $month_items = $table->whereBetween('created_at', [now()->subYears(1), now()])->get();
        $week_items = $table->whereBetween('created_at', [now()->subWeeks(7), now()])->get();
        $day_items = $table->whereBetween('created_at', [now()->subDays(7), now()])->get();

        $data = [
            'total' => $total,
            'daily' => ['total' => count($day_items), 'series' => $this->series($day_items, 7, 86400)],
            'weekly' => ['total' => count($week_items), 'series' => $this->series($week_items, 7, 604800)],
            'monthly' => ['total' => count($month_items), 'series' => $this->series($month_items, 12, 2592000)],
            'yearly' => ['total' => count($year_items), 'series' => $this->series($year_items, 7, 31536000)],
        ];

        return $data;

    }

}
