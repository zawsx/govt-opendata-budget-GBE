<?php namespace DemocracyApps\GB\Http\Controllers;

use DemocracyApps\GB\Accounts\Dataset;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DemocracyApps\GB\Organization;
use DemocracyApps\GB\Accounts\AccountChart;

class DatasetsController extends Controller {

    protected $dataset = null;

    function __construct (Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
        $organization = Organization::find($request->get('organization'));
        $chart = AccountChart::find($request->get('chart'));
        return view('system.dataset.create', array('organization'=>$organization, 'chart'=>$chart));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{

        $rules = ['name' => 'required', 'year'=>'required | digits:4', 'type'=>'in:actual,budget'];


        $this->validate($request, $rules);

        if (! $request->hasFile('data')) {
            return redirect()->back()->withInput()->withErrors(array('file'=>'You must select a file to upload'));
        }
        $this->dataset->name = $request->get('name');
        $this->dataset->organization = $request->get('organization');
        $this->dataset->chart = $request->get('chart');
        $this->dataset->year = $request->get('year');
        $this->dataset->type = $request->get('type');
        if ($request->has('description')) $this->dataset->description = $request->get('description');
        $this->dataset->save();

        $file = $request->file('data');
        $data = array();
        $data['dataset'] = $this->dataset->id;
        $data['userId'] = \Auth::user()->id;
        $name = uniqid('upload');
        $file->move('/var/www/gbe/public/downloads', $name);
        $data['filePath'] = '/var/www/gbe/public/downloads/' . $name;
        $notification = new \DemocracyApps\GB\Utility\Notification;
        $notification->user_id = $data['userId'];
        $notification->status = 'Scheduled';
        $notification->type = 'DatasetUpload';
        $notification->save();
        $data['notificationId'] = $notification->id;
        \Queue::push('\DemocracyApps\GB\Accounts\CSVProcessors\DatasetCSVProcessor', $data);

        return redirect('/system/organizations');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
