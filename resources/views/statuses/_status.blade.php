<li class="d-flex mt-4 mb-4">
  <a class="flex-shrink-0" href="{{ route('users.show', $user->id) }}">
    <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="me-1 gravatar" />
  </a>
  <div class="flex-grow-1 ms-3">
    <h5 class="mt-0 mb-1">{{ $user->name }} <small> / {{ $status->created_at->diffForHumans() }}</small></h5>
    {{ $status->content }}
  </div>

  @can('destroy', $status)
    <form id="delete-form" action="{{ route('statuses.destroy', $status->id) }}" method="POST" >
      {{ csrf_field() }}
      {{ method_field('DELETE') }}
      <button type="submit" class="btn btn-sm btn-danger status-delete-btn">删除</button>
    </form>
  @endcan

@section('scriptsAfterJs')
    <script>
      document.getElementById('delete-form').addEventListener('submit', function(e) {
        e.preventDefault();

      //   Swal.fire({
      //     title: '确定要删除吗？',
      //     text: "删除后将无法恢复！",
      //     icon: 'warning',
      //     showCancelButton: true,
      //     confirmButtonColor: '#3085d6',
      //     cancelButtonColor: '#d33',
      //     confirmButtonText: '确定删除',
      //     cancelButtonText: '取消'
      //   }).then((result) => {
      //     if (result.isConfirmed) {
      //       this.submit();
      //     }
      //   });
      // });

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
      });
      swalWithBootstrapButtons.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // swalWithBootstrapButtons.fire({
          //   title: "Deleted!",
          //   text: "Your file has been deleted.",
          //   icon: "success"
          // });
          this.submit();
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire({
            title: "Cancelled",
            text: "Your imaginary file is safe :)",
            icon: "error"
          });
        }
      });
    });

    </script>
  @stop
</li>
