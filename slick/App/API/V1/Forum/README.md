##Forum API Docs

###General Info
* API URL prefix: /api/v1/forum
* All responses return either code 200 and results, or "error" field and appropriate error code

###Method Summary

* /categories [GET]
* /categories/{ID} [GET]
* /categories/{ID}/threads [GET]
* /boards [GET]
* /boards/{ID} [GET]
* /boards/{ID}/threads [GET]
* /threads [GET]
* /threads/{ID} [GET]
* /threads/{ID}/{PostID} [GET]
- /threads [POST] - (create new thread)
- /threads/{URL} [PATCH] - (edit thread)
- /threads/{URL} [DELETE] - (buries thread)
- /threads/{URL} [POST] - (reply)
- /threads/{URL}/{reply ID} [PATCH] - (edit reply)
- /threads/{URL}/{reply ID} [DELETE] - (bury reply)
* /opts/perms [GET] - (get list of user forum permissions)
- /opts/flag [POST] - (flag a thread or post as spam)
- /opts/like [POST] - (like a thread or post)
- /opts/like [GET] - (check if post is liked)
- /opts/unlike [POST] - (unlike thread/post)
- /opts/move [POST] - (move a thread to different board)
- /opts/lock [POST] - (lock a thread)
- /opts/unlock [POST] - (unlock a thread)
- /opts/sticky [POST] - (set thread to sticky)
- /opts/unsticky [POST] - (unsticky thread)



###Methods

* **/categories [GET]**
	* **Method:** GET
	* **Parameters:** 
		* **strip-html:** bool (optional) - strips out any potential HTML in category names/descriptions 
		* **parse-markdown:** bool (optional) - auto parses markdown from board and category descriptions
	* **Returns:**
		* (array)
			* categoryId (int)
			* name (string)
			* rank (int)
			* description (string)
			* slug (string)
			* boards (array)
				* boardId (int)
				* name (string)
				* slug (string)
				* rank (int)
				* description (string)
* **/categories/{ID} [GET]**
	* **Method:** GET
	* **Parameters:** 
		* **strip-html:** bool (optional) - strips out any potential HTML in category names/descriptions 
		* **parse-markdown:** bool (optional) - auto parses markdown from board and category descriptions
	* **Returns:**
		* categoryId (int)
		* name (string)
		* rank (int)
		* description (string)
		* slug (string)
		* boards (array)
			* boardId (int)
			* name (string)
			* slug (string)
			* rank (int)
			* description (string)
	* {ID} can be a categoryId or category slug
* **/categories/{ID}/threads** [GET]
	* Gets a list of threads for boards within specified category. Refer to /threads endpoint
* **/boards [GET]**
	* **Method:** GET
	* **Parameters:** 
		* **strip-html:** bool (optional) - strips out any potential HTML in board names/descriptions 
		* **parse-markdown:** bool (optional) - auto parses markdown from board description
	* **Returns:**
		* (array)
			* boardId (int)
			* categoryId (int)
			* name (string)
			* slug (string)
			* rank (int)
			* description (string)
* **/boards/{ID} [GET]**
	* **Method:** GET
	* **Parameters:** 
		* **strip-html:** bool (optional) - strips out any potential HTML in board names/descriptions 
		* **parse-markdown:** bool (optional) - auto parses markdown from board description
	* **Returns:**
		* boardId (int)
		* categoryId (int)
		* name (string)
		* slug (string)
		* rank (int)
		* description (string)
	* {ID} can be boardId or board slug
* **/boards/{ID}/threads**
	* Gets a list of threads within board... refer to /threads endpoint
* **/threads [GET]**
	* **Method:** GET
	* **Parameters:** (all optional)
		* **start** (int) - starting row for pagination. defaults 0
		* **limit** (int) - number of rows to return. defaults 25
		* **posted-before** (int)|timestamp - accepts UNIX timestamp or MySQL style timestamp to get threads posted before a certain date. Also can use HTTP header: If-Posted-Before
		* **modified-since** (int)|timestamp - accepts UNIX timestamp or MySQL style timestam to get threads edited or replied in since a certain date. Also can use HTTP header: If-Modified-Since
		* **categories** (string) - comma seperated list of specific category IDs to grab threads from. API converts this into a list of individual board IDs
		* **exclude-categories** (string) - comma seperated list of specific category IDs to exclude from list of threads. API converts this into a list of individual board IDs
		* **boards** (string) - comma seperated list of board IDs to specifically grab threads from
		* **exclude-boards** (string) - comma seperated listof board IDs to exclude threads from list
		* **min-views** (int) - minimum number of thread views to be included in list (e.g only threads with at least 500 views)
		* **max-views** (int) - maximum number of thread views to include thread in list (e.g only threads with less than 500 views)
		* **stickies** (bool) - used to specify grabbing ONLY sticky threads (true) or excluding all sticky threads
		* **locked** (bool) - used to specify grabbing ONLY locked threads or excluding all locked threads from list.
		* **users**  (string) - comma seperated list of user IDs or slugs to grab threads only from specific users
		* **exclude-users** (string) - comma seperated list of user IDs or slugs to exclude any threads by certain users
		* **strip-html** (bool)  - set to true to strip out any possible HTML in output data
		* **parse-markdown** (bool) - set to true to parse markdown content from threads into HTML
		* **no-content** (bool) - set to true to exclude post content from listings (faster)
		* **sort** (string) - choose sorting mode... options are: recent, oldest, time-desc, time-asc, alph-asc, alph-desc, sticky (recent but with sticky posts first)
		* **no-profiles** (bool) - set to true to exclude user profiles for thread OP and most recent reply
	* **Returns:**
		* next (int)|null - gives the next # of entries to use as the "start" parameter. If null, there are no further entries that can be viewed
		* threads (array)
			* topicId (int)
			* userId (int)
			* title (string)
			* url (string)
			* content (string)
			* boardId (int)
			* boardName (string)
			* boardSlug (string)
			* categoryId (int)
			* categoryName (string)
			* categorySlug (string)
			* locked (int)
			* postTime (timestamp)
			* editTime (timestamp)
			* lastPost (timestamp)
			* sticky (int)
			* views (int) 
			* lockTime (timestamp)
			* lockedBy (int)
			* editedBy (int)
			* replies (int)
			* author (array)
				* userId (int)
				* username (string)
				* slug (string)
				* email (string)
				* regDate (timestamp)
				* lastActive (timestamp)
				* lastAuth (timestamp)
				* profile (array)
					* (array)
						* fieldId (int)
						* value (string)
						* label (string)
						* type (string)
						* slug (string)
				* avatar (string)
			* mostRecent
				* postId (int)
				* userId (int)
				* content (string)
				* postTime (timestamp)
				* editTime (timestamp)
				* editedBy (timestamp)
				* author
					* see fields for previous "author" field
* **/threads/{ID} [GET]**
	* **Method:** GET
	* **Parameters:** (all optional)
		* **start** (int) - starting row of replies for pagination. defaults 0
		* **limit** (int) - number of reply rows to return. defaults 20
		* **strip-html** (bool)  - set to true to strip out any possible HTML in output data
		* **parse-markdown** (bool) - set to true to parse markdown content from threads into HTML		
		* **no-profiles** (bool) - set to true to exclude user profiles from data
		* **thread-only** (bool) - set to true to return only thread data, no replies
		* **replies-only** (bool) - set to true to return only replies, no main thread data		
	* **Returns:**
		* thread (array)
			* topicId (int)
			* boardId (int)
			* userId (int)
			* title (string)
			* url (string)
			* content (string)
			* locked (int)
			* postTime (timestamp)
			* editTime (timestamp)
			* lastPost (timestamp)
			* sticky (int)
			* views (int)
			* lockTime (int)
			* lockedBy (int)
			* editedBy (int)
			* boardName (string)
			* boardSlug (string)
			* categoryId (int)
			* categoryName (string)
			* categorySlug (string)
			* replies (int)
			* author (array)
				* userId (int)
				* username (string)
				* email (string)
				* regDate (timestamp)
				* lastActive (timestamp)
				* lastAuth (timestamp)
				* profile (array)
					* (array)
						* fieldId (int)
						* value (string)
						* label (string)
						* type (string)
						* slug (string)
				* avatar (string)
		* replies (array)
			* postId (int)
			* userId (int)
			* content (string)
			* postTime (timestamp)
			* editTime (timestamp)
			* editedBy (int)
			* author (array)
				* see previous author field for reference.
* **/threads/{ID}/{REPLYID} (get single reply)**
	* **METHOD:** GET
	* **Params:**
		* no-profiles (bool)(optional)
		* strip-html (bool)(optional)
		* parse-markdown (bool)(optional)
	* **Returns:**
		* post
			* postId (int)
			* userId (int)
			* topicId (int)
			* content (string) 
			* postTime (timestamp)
			* editTime (timestamp)
			* editedBy (timestamp)
			* author (array)
				* (user profile data)
* **/threads (create thread)**
	* **Method:** POST
	* **Params:**
		* boardId (int)
		* title (string)
		* content (string)
		* parse-markdown (bool)(optional)
	* **Returns:**
		* thread
			* topicId (int)
			* boardId (int)
			* userId (int)
			* title (string)
			* url (string)
			* content (string)
			* locked (int)
			* postTime (timestamp)
			* editTime (timestamp default null)
			* lastPost (timestamp)
			* sticky (int)
			* views (int)
			* lockTime (timestamp default null)
			* lockedBy (int)
			* editedBy (int)
* **/threads/{ID} (edit thread)**
	* **Method:** PATCH
	* **Params:**
		* title (string)
		* content (string)
		* parse-markdown (bool)(optional)
	* **Returns:**
		* thread
			* topicId (int)
			* boardId (int)
			* userId (int)
			* title (string)
			* url (string)
			* content (string)
			* locked (int)
			* postTime (timestamp)
			* editTime (timestamp)
			* lastPost (timestamp)
			* sticky (int)
			* views (int)
			* lockTime (timestamp)
			* lockedBy (int)
			* editedBy (int)
* **/threads/{ID} (delete thread)**
	* **Method:** DELETE
	* **Params:** none
	* **Returns:**
		* result: "success"
* **/threads/{ID} (post reply)**
	* **Method:** POST
	* **Params:**
		* content (string)
		* parse-markdown (bool)(optional)
	* **Returns:**
		* post
			* postId (int)
			* topicId (int)
			* userId (int)
			* content (string)
			* postTime (timestamp)
* **/threads/{ID}/{REPLYID} (edit reply)**
	* **Method:** PATCH
	* **Params:**
		* content (string)
		* parse-markdown (bool)(optional)
	* **Returns:**
		* post
			* postId (int)
			* topicId (int)
			* userId (int)
			* content (string)
			* postTime (timestamp)
			* editTime (timestamp)
			* editedBy (int)
* **/threads/{ID}/{REPLYID} (delete reply)**
	* **Method:** DELETE
	* **Params:** none
	* **Returns:**
		* result: "success"
* **/opts/perms**
	* **Method:** GET
	* **Params:** 
		* type (string)(optional) - type of item to check for.. category, topic or board.
		* id (int)(optional) - topicId, boardId or categoryId
	* **Returns:**
		* perms
			* (dictionary list of forum permissions)
	* List of permissions under "perms" field is formatted <perm key> = true|false. If true, means the user does indeed have that permission available. If false, no access
	* If type and id is set, it will check permissions for those specific items. Permissions can change depending on what items are being dealt with. (e.g moderator only has mod permissions in certain boards)
* **/opts/flag**
	* **Method:** POST
	* **Params:**
		* type (string) - topic or post. "thread" can also be used instead of topic (same thing)
		* id (int) - topicId or postId
	* **Returns:**
		* result (bool)
	* Flags a post or thread as spam, notifies any relevant moderators.
* **/opts/like**
	* **Method:** POST
	* **Params:**
		* type (string) - topic or post. "thread" can also be used instead of topic (same thing)
		* id (int) - topicId or postId	
	* **Returns:**
		* result (bool)
	* "likes" / upvotes a post or a thread
* **/opts/like** (check if liked)
	* **Method:** GET
	* **Params:**
		* type (string) - topic or post
		* id (int) - topicId or postId	
	* **Returns:**
		* liked (bool)
	* returns true if post has been "liked" by user already. False otherwise.
* **/opts/unlike**
	* **Method:** POST
	* **Params:**
		* type (string) - topic or post
		* id (int) - topicId or postId	
	* **Returns:**
		* result (bool)
	* removes a users "like" from post or thread.
* **/opts/lock**
	* **Method:** POST
	* **Params:**
		* id (int) - topicId of thread you want to lock
	* **Returns:**
		* result (bool)
	* Sets thread to "locked" state
* **/opts/unlock**
	* **Method:** POST
	* **Params:**
		* id (int) - topicId of thread you want to lock
	* **Returns:**
		* result (bool)
	* Removes "locked" state from thread
* **/opts/sticky**
	* **Method:** POST
	* **Params:**
		* id (int) - topicId of thread you want to sticky
	* **Returns:**
		* result (bool)
	* Sets thread to "sticky" state
* **/opts/unsticky**
	* **Method:** POST
	* **Params:**
		* id (int) - topicId of thread you want to un-sticky
	* **Returns:**
		* result (bool)
	* Removes "sticky" state from thread
* **/opts/move**
	* **Method:** POST
	* **Params:**
		* from-type (string) - type of item you want to move.. options: topic, thread (alias)
		* from-id (int) - ID of item to move, e.g the thread topicId
		* to-type (string) - type of area to move the item into.. options: board
		* to-id (int) - ID of area to move item minto, e.g the boardId
	* **Returns:**
		* result (bool)
	* Moves a thread from one board to another.

