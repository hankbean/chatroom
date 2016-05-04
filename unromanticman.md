#unromanticman

###2016/05/03
1. 整合目前所有API與修正留言頁面參數

###2016/04/27
1. 新增getPermission API 用於檢定使用者權限，其對應的js為checkPermission.js 只要引入此js檔，可達到 '未登入' 自動跳轉至登入頁面。
2. 串接使用者登入API、使用者註冊API
3. 更新sql檔案 增加性別保密欄位，並初始化內部資料。
4. 修改userRegister API，已改為先檢驗是否已存在使用者，再執行insert建立使用者。
5. 增加jquery.cookie.js

###2016/04/25
1. 將ALL的value設定成1(即id=1)
2. 修正app.js與chatroom.html內相關參數


###2016/04/20
1. github 上傳教學
2. node.js server端增加mysql模組 可以將資料直接寫入資料庫

###2016/04/19
1. 完成了github repository 建立
2. 初始化專案內容
3. 上傳node.js server 與 前端index、聊天室模板
