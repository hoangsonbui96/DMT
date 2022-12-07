<?php

namespace App\Http\Controllers\Admin;

use App\Document;
use App\MasterData;
use App\Menu;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * Class DocumentController
 * @package App\Http\Controllers\Admin
 */
class DocumentController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $array = $this->RoleView('DocumentList', []);
        $this->menu = $array['menu'];
    }

    public function show(Request $request, $orderBy = 'created_at', $sortBy = 'desc')
    {

        $this->authorize('view', $this->menu);
        $recordPerPage = $this->getRecordPage();

        //get list document
        $document = Document::query()->select('users.FullName', 'master_data.Name', 'documents.*')
            ->join('master_data', 'master_data.DataValue', '=', 'documents.dType')
            ->leftJoin('users', 'users.id', '=', 'documents.byUser')
            ->orderBy($orderBy, $sortBy);

        // Search in columns
        $this->data['request'] = $request->query();
        $one = Document::query()
            ->select('users.FullName', 'master_data.Name', 'documents.dType', 'documents.dName', 'documents.created_at', 'documents.byUser')
            ->join('master_data', 'master_data.DataValue', '=', 'documents.dType')
            ->leftJoin('users', 'users.id', '=', 'documents.byUser')->first();

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

        //redirect to the last page if current page has no record
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
        return $this->viewAdminLayout('document-list', $this->data);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function documentView(Request $request)
    {
        $documents = Document::query()->get();
        $categories = MasterData::query()->select('Name', 'DataValue')
            ->where('DataKey', 'TL')->get();
        $arrDocument = [];
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $listDocumentOfCategory = Document::query()->select('id', 'dName', 'fileName', 'dUrl', 'typeUpload')
                    ->where(function ($query) {
                        $query->where('userView', 'like', '%' . Auth::user()->id . '%')
                            ->orWhereNull('userView');
                    })
                    ->where('dType', $category->DataValue)->get();

                if (count($listDocumentOfCategory) > 0) {
                    $arrDocument[$category->DataValue]['parent'] = $category;
                    foreach ($listDocumentOfCategory as $doc) {
                        $arrDocument[$category->DataValue]['children'][] = $doc;

                        //check url not exit or not show
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

                            //check file not exits
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

        return $this->viewAdminLayout('document-view', $this->data);
    }

    public function insertUpdateDoc(Request $request, $id = null)
    {

        if (count($request->input()) == 0) {
            return abort('404');
        }
        try {
            $arrCheck = [
                'dName'      => 'required|string|min:1',
                'dType'      => 'required|string',
                'userView'   => 'nullable|array',
                'typeUpload' => 'nullable|min:1',
            ];
            $messages = [
                'dName.required' => 'Tên tài liệu không được để trống.',
                'dType.required' => 'Chưa chọn kiểu văn bản.',
            ];
            $modeUpdate = array_key_exists('id', $request->input());
            if ($modeUpdate) {
                $arrCheck['id'] = 'integer|min:1';
            }
            if ($request['typeUpload'] == 1) {
                $arrCheck['dUrl'] = ['required', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/'];
                $messages['dUrl.required'] = 'Đường dẫn tài liệu không được để trống.';
                $messages['dUrl.regex'] = 'Đường dẫn tài liệu không hợp lệ.';
            } else {
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
            if (isset($validated['userView'])) {
                $one->userView = ',' . implode(',', $validated['userView']) . ',';
            }

            //nếu update chuyển kiểu định dạng sẽ làm trống định dạng cũ
            if ($validated['typeUpload'] == 0) {
                $one->dUrl = '';
            } else {
                $one->fileName = '';
            }

            $one->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


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
                return $this->viewAdminIncludes('document-detail', $this->data);
            } else {
                return "";
            }
        } else {
            return $this->viewAdminIncludes('document-detail', $this->data);
        }
    }

    public function routeDownloadDoc(Request $request)
    {
        return Response::download($request['path']);
    }
}
