<div class="box tbl-top">
    <div class="box-body table-responsive no-padding table-scroll">
        <table class="table table-bordered table-striped" name="table">
            <thead class="thead-default">
                {{ $columnsTable ?? '' }}
            </thead>
            <tbody>
                {{ $dataTable ?? '' }}
            </tbody>
        </table>
    </div>
</div>

{{ $pageTable ?? '' }}
