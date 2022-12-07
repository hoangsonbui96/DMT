<?php
	namespace App\Http\Controllers\Admin;

	use App\Model\Absence;
	use App\Exports\AbsencesExport;
	use App\Exports\AbsencesReportExport;
	use App\MasterData;
	use App\Menu;
	use App\RoleGroupScreenDetailRelationship;
	use App\RoleUserScreenDetailRelationship;
	use App\Room;
	use App\User;
	use App\RoleScreenDetail;
	use Carbon\Carbon;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Support\Facades\Validator;
	use Maatwebsite\Excel\Facades\Excel;
	use App\CalendarEvent;
	use Carbon\CarbonInterval;

	/**
	 * Controller screen Absence
	 * Class AbsenceController
	 * @package App\Http\Controllers\Admin
	 */
	class TotalReportController extends AdminController {
		protected $startTime = '08:30';
		protected $endTime = '17:30';
		protected $timeOutAm = '12:00';
		protected $timeInPm = '13:00';
		protected $view;
		protected $export;
		const KEYMENU= array(
	        "view" => "PartnerList",
	        "export" => "TotalReportExport",
	    );
		/**
		 * TotalReportController constructor.
		 * @param Request $request
		 * Check role view, export
		 */
		public function __construct(Request $request) {
			parent::__construct($request);
			$this->middleware('auth');
			$array = $this->RoleView('TotalReport',['TotalReport']);
			$this->data['menu'] = $array['menu'];
	        foreach (self::KEYMENU as $key => $value) {
	            foreach ($array['role'] as $row) {
	                if($value == $row->alias)
	                    $this->$key = $row;
	            } 
	        }
		}

		/**
		 * @param Request $request
		 * @return View (total-report)
		 * @throws \Illuminate\Auth\Access\AuthorizationException
		 *
		 */
		public function index(Request $request) {
			$this->authorize('view', $this->menu);
			$this->data['request'] = $request->query();

			$UserSelect = $request->has('UserID') ? $request['UserID'] : Auth::user()->id;
			$user = User::query()->find($UserSelect);

			$this->data['user'] = $user;
			$this->data['selectUser'] = $this->GetListUser();
			$this->data['export'] = $this->export;

			// start
			// end

			return $this->viewAdminLayout('total-report', $this->data);
		}

		/**
		 * @param Request $request
		 * @return View (total-report)
		 * @throws \Illuminate\Auth\Access\AuthorizationException
		 *
		 */
		public function Search(Request $request) {
			return $this->viewAdminLayout('total-report', $this->data);
		}

		/**
		 * @param $date
		 * @return bool
		 */
		public function checkHoliday($date) {
			//check weekend
			if($date->isWeekend()) {
				//kiem tra xem co phải ngày làm bù ko
				$queryOne = CalendarEvent::query()
						->where('StartDate', '<=', $date->toDateString())
						->where('EndDate', '>=', $date->toDateString())
						->where('Type', 0)
						->where('CalendarID', 1)
						->first();
				return $queryOne ? false : true;
			} else {
				//kiểm tra xem có phải ngày nghỉ lễ ko
				$queryOne = CalendarEvent::query()
						->where('StartDate', '<=', $date->toDateString())
						->where('EndDate', '>=', $date->toDateString())
						->where('Type', '!=', 0)
						->where('CalendarID', 1)
						->first();
				return $queryOne ? true : false;
			}
		}
	}
?>