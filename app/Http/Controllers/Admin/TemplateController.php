<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\TemplateGroup;
use App\Template;

use Cloudstack\CloudStackClient;
use Illuminate\Http\Request;
use App\Http\Requests\TemplateRequest;

class TemplateController extends Controller {

    private $acs;
    
    public function __construct(CloudStackClient $acs)
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
        
        $this->acs = $acs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $groups = TemplateGroup::with('templates')->get();

        return view('admin.template.index')->with(compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        $templates = $this->acs->listTemplates(['templatefilter' => 'executable']);

        return view('admin.template.create')->with(compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(TemplateRequest $request)
    {
        // Do we have an uploaded image?
        // Handle display image
        if ($request->hasFile('display_img'))
        {
            // Save file somewhere useful
            $path = public_path() . '/img/';
            $filename = $request->file('display_img')->getFilename() . '.' . $request->file('display_img')->guessExtension();

            $request->file('display_img')->move($path, $filename);
        }

        // Create the template group
        $group = TemplateGroup::create(['name'        => $request['name'],
                                        'type'        => $request['type'],
                                        'display_img' => (isset($filename)) ? $filename : ''
        ]);

        if ($request['templates'])
        {
            // We have some templates selected
            foreach ($request['templates'] as $id => $val)
            {
                if (0 == $val)
                {
                    // Template wasn't selected for inclusion
                    continue;
                }

                $template = new Template(['template_id' => $id,
                                              'size'        => ('SaaS' == $group->type) ? '0' : $request['templateSize'][$id],
                                              'price'       => '0']);

                $group->templates()->save($template);

                unset($template, $id, $val);
            }

        }
        return redirect()->route('admin.template.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // NOT CURRENTLY IN USE
        /*
        $group = TemplateGroup::findOrFail($id);
        $templates = $group->templates;

        return view('admin.template.show')->with(compact('group', 'templates'));
        */
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $group = TemplateGroup::findOrFail($id);
        $templates = $this->acs->listTemplates(['templatefilter' => 'executable']);
        $checkedIDs = [];
        foreach ($group->templates as $template)
        {
            $checkedIDs[] = $template->template_id;
        }

        return view('admin.template.edit')->with(compact('group', 'templates', 'checkedIDs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        // Grab our template
        $tg = TemplateGroup::findOrFail($id);

        $templates = $tg->templates;

        // Make an array of template IDs: existing templates and request templates.
        $currentTemplates = [];

        foreach ($templates as $template)
        {
            $currentTemplates[] = $template->template_id;
        }

        $newTemplates = array_keys($request->templates);

        $templatesDelete = array_diff($currentTemplates, $newTemplates);
        $templatesAdd = array_diff($newTemplates, $currentTemplates);

        foreach ($templatesDelete as $td)
        {
            $tpl = Template::where('template_id', '=', $td)->where('template_group_id', '=', $tg->id)->first();
            $tpl->delete();
        }

        foreach ($templatesAdd as $ta)
        {
            $newTemplate = new Template(['template_id' => $ta,
                                      'size'        => ('SaaS' == $request->type) ? '0' : $request['templateSize'][$ta],
                                      'price'       => '0']);

            $tg->templates()->save($newTemplate);

            unset($newTemplate);
        }

        // Update the template group record
        $tg->name = $request->name;
        $tg->type = $request->type;

        // Check to see if we have a new image to work with
        if ($request->hasFile('display_img'))
        {
            // Save file somewhere useful
            $path = public_path() . '/img/';
            $filename = $request->file('display_img')->getFilename() . '.' . $request->file('display_img')->guessExtension();

            $request->file('display_img')->move($path, $filename);
            $tg->display_img = $filename;
        }

        $tg->save();

        flash()->success('Successfully updated template group: ' . $tg->name);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        TemplateGroup::destroy($id);

        return 1;
    }

}
