   <!-- Modal -->
   <div class="modal fade text-left" id="infoModal" tabindex="-1" aria-labelledby="myModalLabel160" aria-modal="true" role="dialog">
       <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
           <div class="modal-content">
               <div class="modal-header bg-primary">
                   <h5 class="modal-title white" id="myModalLabel160">訊息
                   </h5>
                   <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                           <line x1="18" y1="6" x2="6" y2="18"></line>
                           <line x1="6" y1="6" x2="18" y2="18"></line>
                       </svg>
                   </button>
               </div>
               <div class="modal-body">
                   <div id="info"></div>
               </div>
               <div class="modal-footer">
                   <!-- <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                       <i class="bx bx-x d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">Close</span>
                   </button> -->
                   <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal">
                       <i class="bx bx-check d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">關閉</span>
                   </button>
               </div>
           </div>
       </div>
   </div>

   <!-- 刪除modal -->
   <div class="modal fade text-left" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
           <div class="modal-content">
               <div class="modal-header bg-danger">
                   <h5 class="modal-title white" id="deleteModalLabel">刪除
                   </h5>
                   <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                           <line x1="18" y1="6" x2="6" y2="18"></line>
                           <line x1="6" y1="6" x2="18" y2="18"></line>
                       </svg>
                   </button>
               </div>
               <div class="modal-body">
                   <div id="delete-info"></div>
                   <p class="text-danger text-end">確認要刪除此筆資料？</p>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                       <i class="bx bx-x d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">返回</span>
                   </button>
                   <button type="button" class="btn btn-danger ms-1" data-bs-dismiss="modal" id="confirmDelete">
                       <i class="bx bx-check d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">刪除</span>
                   </button>
               </div>
           </div>
       </div>
   </div>



   <!-- 老師的 -->
   <!-- <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       <div class="modal-dialog">
           <div class="modal-content">
               <div class="modal-header">
                   <h1 class="modal-title fs-5" id="exampleModalLabel">訊息</h1>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div id="info"></div>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-primary" data-bs-dismiss="modal">關閉</button>
               </div>
           </div>
       </div>
   </div> -->

   <!-- 紅色訊息 -->
   <!-- <div class="modal fade text-left" id="infoModal" tabindex="-1" aria-labelledby="myModalLabel120" style="display: none;" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
           <div class="modal-content">
               <div class="modal-header bg-danger">
                   <h5 class="modal-title white" id="myModalLabel120">錯誤訊息
                   </h5>
                   <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                           <line x1="18" y1="6" x2="6" y2="18"></line>
                           <line x1="6" y1="6" x2="18" y2="18"></line>
                       </svg>
                   </button>
               </div>
               <div class="modal-body">
                   <div id="info"></div>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger ms-1" data-bs-dismiss="modal">
                       <i class="bx bx-check d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">關閉</span>
                   </button>
               </div>
           </div>
       </div>
   </div> -->