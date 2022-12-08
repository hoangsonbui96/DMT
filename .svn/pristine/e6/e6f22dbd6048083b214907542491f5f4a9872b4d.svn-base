<?php

namespace Modules\Document\Http\Controllers;

use Modules\Document\Entities\Document;
use App\Http\Controllers\Admin\AdminController;
use App\MasterData;
use App\RoleUserScreenDetailRelationship;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class DocumentController
 * @package Modules\Document\Http\Controllers
 */
class DocumentController extends AdminController
{
    protected $add;
    protected $edit;
    protected $delete;
    protected $view;

    const KEYMENU = array(
        "add" => "DocumentListAdd",
        "view" => "DocumentList",
        "edit" => "DocumentListEdit",
        "delete" => "DocumentListDelete",
    );

    /**
     * DocumentController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('DocumentList', ['DocumentList']);
        $this->menu = $array['menu'];
        foreach (self::KEYMENU as $key => $value) {
            foreach ($array['role'] as $row) {
                if ($value == $row->alias)
                    $this->$key = $row;
            }
        }
    }

    /**
     * @param Request $request
     * @param string $orderBy
     * @param string $sortBy
     * @return Application|RedirectResponse|Redirector|View
     * @throws AuthorizationException
     */
    public function show(Request $request, $orderBy = 'created_at', $sortBy = 'desc')
    {
        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();
        $loggedUserId = Auth::user()->id;
        $activeRole = 1;

        //Get list document
        $document = Document::query()->select('users.FullName', 'master_data.Name', 'documents.*')
            ->join('master_data', 'master_data.DataValue', '=', 'documents.dType')
            ->leftJoin('users', 'users.id', '=', 'documents.byUser')
            ->orderBy($orderBy, $sortBy);
        
        // check user role
        $checkUserRole = RoleUserScreenDetailRelationship::query()
            ->where('user_id', $loggedUserId)
            ->where('permission', $activeRole);
        $roleEditAlias = Arr::get(self::KEYMENU, 'edit');
        $roleDeleteAlias = Arr::get(self::KEYMENU, 'delete');
        $userRoleEdit = $checkUserRole->where('screen_detail_alias', $roleEditAlias)->first();
        $userRoleDelete = $checkUserRole->orwhere('screen_detail_alias', $roleDeleteAlias)->first();
        if(!$userRoleEdit || !$userRoleDelete){
            $document = $document->where(function ($query) use ($loggedUserId) {
                $query->orWhere('documents.userView', 'like', '%,' . $loggedUserId . ',%')
                      ->orWhere('documents.userView', '');
            });
        }

        // Search in columns
        $this->data['request'] = $request->query();
        $one = Document::query()
            ->select('users.FullName', 'master_data.Name','documents.fileName', 'documents.dType', 'documents.dName', 'documents.created_at', 'documents.byUser')
            ->join('master_data', 'master_data.DataValue', '=', 'documents.dType')
            ->leftJoin('users', 'users.id', '=', 'documents.byUser')->first();

        // If exists document query
        if ($one) {
            $one = $one->toArray();
            if (array_key_exists('search', $request->input())) {
                $document = $document->where(function ($query) use ($one, $request) {

                    foreach ($one as $key => $value) {
                        if ($key == 'FullName') {
                            $query->orWhere('users.' . $key, 'like', '%' . $request->input('search') . '%');
                        } elseif ($key == 'Name') {
                            $query->orWhere('master_data.' . $key, 'like', '%' . $request->input('search') . '%');
                        } else {
                            $query->orWhere('documents.' . $key, 'like', '%' . $request->input('search') . '%');
                        }
                    }
                    $query->orWhereRaw('(DATE_FORMAT(documents.created_at,"%d/%m/%Y")) like ?', '%' . $request->input('search') . '%');
                });
            }
        }
        $count = $document->count();

        //Pagination
        $document = $document->paginate($recordPerPage);
        $query_string = str_replace($request->url(), '', $request->fullUrl());
        $sort_link = ($sortBy == 'desc' ? 'asc' : 'desc') . "/" . $query_string;
        $sort = ($sortBy == 'asc' ? 'asc' : 'desc');
        parse_str(str_replace('?', '', $query_string), $query_array);

        //Redirect to the last page if current page has no record
        if ($document->count() == 0) {
            if (array_key_exists('page', $query_array)) {
                if ($query_array['page'] > 1) {
                    $query_array['page'] = $document->lastPage();
                    $query_string = http_build_query($query_array);
                    $fullUrl = $request->url() . '?' . $query_string;
                    return redirect($fullUrl);
                }
            }
        }
        $this->data['stt'] = $this->numericalOrderSort($query_array, $recordPerPage, $sort, $count);
        $this->data['document'] = $document;
        $this->data['query_array'] = $query_array;
        $this->data['sort_link'] = $sort_link;
        $this->data['sort'] = $sort;
        $this->data['add'] = $this->add;
        $this->data['edit'] = $this->edit;
        $this->data['delete'] = $this->delete;
        return view("document::layouts.document-list", $this->data);
    }

    public function checkDocument(Request $request)
    {
        $type_upload_url = 1;
        $type_upload_file = 0;

        $type_upload = $request->type_upload;
        $path_document = $request->path_file;
        if($type_upload == $type_upload_file){
            if (file_exists(public_path($path_document))) {
                return response()->file(\public_path($path_document));
            }
            return response()->json(['errors' => 'Không tìm thấy file']);
        } else if($type_upload == $type_upload_url){
            // $contents = file_get_contents($path_document);
            // $name_contents = basename($path_document);
            // Storage::disk('public')->put('/files/shares/' . $name_contents, $contents);
            return response($path_document);
        } else{
            return response()->json(['errors' => 'Không tìm thấy file']);
        }
    }

    public function showDocument($id_document){
        $path_document = Document::where('id', $id_document)->select('fileName')->first();
        $this->data['id_document'] = $id_document;
        $this->data['path_document'] = $path_document;
        $this->data['url_document'] = $this->getSignedUrl($id_document, 'admin.document.get_document');
        return view('document::layouts.document-show', $this->data);
    }

    public function getDocument($id_document)
    {
        $type_upload_url = 1;
        $type_upload_file = 0;

        $document = Document::where('id', $id_document)->first();
        $type_upload = $document->typeUpload;
        if($type_upload == $type_upload_file){
            $path_document = $document->fileName;
            return response()->file(\public_path($path_document));
        }else{
            $path_document = $document->dUrl;
            $client = new \GuzzleHttp\Client();
            $res = $client->get($path_document);
            $content = (string) $res->getBody();
            return response($content);
        }
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function documentView(Request $request)
    {
        $documents = Document::query()->get();
        $categories = MasterData::query()->select('Name', 'DataValue')
            ->where('DataKey', 'TL')->get();
        $arrDocument = [];
        $key_document = ['id', 'dName', 'fileName', 'dUrl', 'typeUpload'];
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $listDocumentOfCategory = Document::query()->select($key_document)
                    ->where(function ($query) {
                        $query->where('userView', 'like', '%' . Auth::user()->id . '%')
                            ->orWhereNull('userView');
                    })
                    ->where('dType', $category->DataValue)->get();

                if (count($listDocumentOfCategory) != 0) {
                    $arrDocument[$category->DataValue]['parent'] = $category;
                    foreach ($listDocumentOfCategory as $doc) {
                        $arrDocument[$category->DataValue]['children'][] = $doc;
                        //Check url not exit or not show
                        if ($doc->typeUpload == 1) {
                            $url = trim($doc->dUrl);
                            $url_headers = @get_headers($url);
                            if ($url_headers != false) {
                                foreach ($url_headers as $key => $value) {
                                    if (strtolower($value) == ('x-frame-options: sameorigin' || 'x-frame-options: deny' || 'x-frame-options: allow-from')) {
                                        $doc->check = 'false';
                                    }
                                }
                            } else {
                                $doc->check = 'urlNotFound';
                            }
                        } else {
                            //Check file not exits
                            $filename = $doc->fileName;
                            $pathInfoExtension = pathinfo($filename, PATHINFO_EXTENSION);
                            $arrExtension = ['doc', 'docx', 'xls', 'xlsx'];
                            if (in_array($pathInfoExtension, $arrExtension)) {
                                $doc->check = 'InfoExtensionNotOk';
                            }
                            if (!file_exists($filename)) {
                                $doc->check = 'fileFalse';
                            }
                        }
                    }
                }
            }
        }
        $this->data['masterData'] = $categories;
        $this->data['request'] = $request->query();
        $this->data['documents'] = $documents;
        $this->data['arrDocument'] = $arrDocument;
        return \view("document::layouts.document-view", $this->data);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return string|void
     */
    public function insertUpdateDoc(Request $request, $id = null)
    {
//        if (count($request->input()) == 0) {
//            return abort('404');
//        }
        try {
            $arrCheck = [
                'dName' => 'required|string|min:1',
                'dType' => 'required|string',
                'dDescription' => 'max:250',
                'userView' => 'nullable|array',
                'typeUpload' => 'nullable|min:1',
            ];
            $messages = [
                'dName.required' => 'Tên tài liệu không được để trống.',
                'dType.required' => 'Chưa chọn kiểu văn bản.',
                'dDescription.max' => 'Mô tả không được quá 250 kí tự',
            ];
            $modeUpdate = array_key_exists('id', $request->input());
            $all_user = array_key_exists('selectAllUser', $request->input());
            if ($modeUpdate) {
                $arrCheck['id'] = 'integer|min:1';
            }
            if(empty($all_user) && empty($request->userView)){
                $messages = 'Chưa chọn người xem.';
                return response()->json(['errors' => $messages]);
            }
            if ($request['typeUpload'] == 1) {
                $arrCheck['dUrl'] = ['required', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/'];
                $messages['dUrl.required'] = 'Đường dẫn tài liệu không được để trống.';
                $messages['dUrl.regex'] = 'Đường dẫn tài liệu không hợp lệ.';
            } elseif(!$modeUpdate){
                $arrCheck['fileName'] = 'required';
                $messages['fileName.required'] = 'Chưa chọn file.';
            }
            $validator = Validator::make($request->all(), $arrCheck, $messages);

            if ($validator->fails()) {
                return $this->jsonArrErrors($validator->errors()->first());
            }
            $validated = $validator->validate();
            $one = !$modeUpdate ? new Document() : Document::find($validated['id']);
            foreach ($validated as $key => $value) {
                if (Schema::hasColumn('documents', $key)) {
                    $one->$key = $value;
                }
            }
            $one->byUser = Auth::user()->id;
            $one->dDescription = $request->dDescription;

            //lưu file
            if($request->fileName != null && $request['typeUpload'] == 0){
                $file = $request->fileName;
                $file_extension = $file->getClientOriginalExtension();
                $one->fileName = './storage/app/public/files/shares/' . $file->getClientOriginalName();
                if (strcasecmp($file_extension, 'pdf') === 0) {
                    $file->move(public_path('storage/app/public/files/shares'), $file->getClientOriginalName());
                }else{
                    $messages = 'file tải lên phải là pdf';
                    return response()->json(['errors' => $messages]);
                }
            }

            // kiểm tra xem có chọn tất cả người dùng hay không
            if($all_user){
                $one->userView = '';
            }else if(empty($one->userView)){
                $one->userView = ',' . Auth::user()->id. ',';
            }else if (isset($validated['userView'])) {
                $one->userView = ',' . implode(',', $validated['userView']) . ',';
            }
            //Nếu update chuyển kiểu định dạng sẽ làm trống định dạng cũ
            if ($validated['typeUpload'] == 0) {
                $one->dUrl = '';
            } else {
                $one->fileName = '';
            }
            $one->save();
            return 1;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * @param null $id
     * @param null $del
     * @return View|int|string
     */
    public function showDetail($id = null, $del = null)
    {
        $this->data['masterData'] = MasterData::query()->select('Name', 'DataValue')
            ->where('DataKey', 'TL')->get();
        $this->data['user'] = $this->GetListUser(self::USER_ACTIVE_FLAG);
        if ($id != null) {
            $this->data['infoDoc'] = Document::find($id);
            if ($del == 'del') {
                $one = Document::find($id);
                if ($one != null) {
                    $one->delete();
                }
                return 1;
            }
            if ($this->data['infoDoc']) {
                if (!is_null($this->data['infoDoc']->userView)) {
                    $this->data['infoDoc']->userView = explode(',', $this->data['infoDoc']->userView);
                } else {
                    $this->data['infoDoc']->userView = [];
                }
//                return $this->viewAdminIncludes('document-detail', $this->data);
                return view('document::includes.document-detail', $this->data);
            } else {
                return "";
            }
        } else {
//            return $this->viewAdminIncludes('document-detail', $this->data);
            return view('document::includes.document-detail', $this->data);
        }
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function routeDownloadDoc(Request $request): BinaryFileResponse
    {
        return Response::download($request['path']);
    }

    public function getSignedUrl($id_document, $name_route, $life_time = 10){
        if(empty($id_document) || empty($name_route)){
            return abort(404);
        }
        return URL::temporarySignedRoute($name_route, now()->addSeconds($life_time),['id' => $id_document]);
    }
}
