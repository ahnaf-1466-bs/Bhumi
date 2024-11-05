"use strict";(self.webpackChunkfuse=self.webpackChunkfuse||[]).push([[188],{2188:(ct,A,d)=>{d.r(A),d.d(A,{FeedbackActivityModule:()=>nt});var f=d(6895),m=d(9197),S=d(1056),t=d(4650),h=d(2340),x=d(3900),g=d(9646),b=d(529);let T=(()=>{class i{constructor(e,s){this.http=e,this.router=s}getFeedbackQuestion(e,s){let n=localStorage.getItem("auth-token"),r=localStorage.getItem("user-id"),l=new FormData;return l.append("wsfunction","mod_coursefeedback_get_coursefeedback_questions"),l.append("wstoken",n),l.append("moodlewsrestformat","json"),l.append("feedbackid",e),l.append("cmid",s),l.append("userid",r),this.http.post(`${h.N.baseURL}/webservice/rest/server.php`,l).pipe((0,x.w)(u=>(0,g.of)(u)))}}return i.\u0275fac=function(e){return new(e||i)(t.LFG(b.eN),t.LFG(m.F0))},i.\u0275prov=t.Yz7({token:i,factory:i.\u0275fac,providedIn:"root"}),i})(),Z=(()=>{class i{constructor(e,s){this.http=e,this.router=s,this.feedBacks=[],this.courseComment={}}submitFeedback(e,s,n,r,l){this.feedBacks=e,this.courseComment=s;let u=localStorage.getItem("auth-token"),c=localStorage.getItem("user-id"),a=new FormData;a.append("wsfunction","mod_coursefeedback_save_feedback_responses"),a.append("wstoken",u),a.append("moodlewsrestformat","json"),a.append("feedbackid",n),a.append("cmid",r),a.append("courseid",l),a.append("userid",c),a.append("responses[0][questionid]","0"),a.append("responses[0][response]",this.feedBacks[0].rating),a.append("responses[0][inputtype]","int"),a.append("responses[1][questionid]","0"),a.append("responses[1][response]",this.courseComment.answer),a.append("responses[1][inputtype]","text");for(let p=2;p<this.feedBacks.length;p++)a.append("responses["+p+"][questionid]",this.feedBacks[p].questionid),a.append("responses["+p+"][response]",this.feedBacks[p].rating),a.append("responses["+p+"][inputtype]","int");return this.http.post(`${h.N.baseURL}/webservice/rest/server.php`,a).pipe((0,x.w)(p=>(0,g.of)(p)))}}return i.\u0275fac=function(e){return new(e||i)(t.LFG(b.eN),t.LFG(m.F0))},i.\u0275prov=t.Yz7({token:i,factory:i.\u0275fac,providedIn:"root"}),i})();var v=d(4006),q=d(8951),N=d(7477);let J=(()=>{class i{constructor(e,s){this.http=e,this.router=s}autoDone(e){let s=localStorage.getItem("auth-token"),n=new FormData;return n.append("wsfunction","mod_coursefeedback_view_coursefeedback"),n.append("wstoken",s),n.append("moodlewsrestformat","json"),n.append("coursefeedbackid",e),this.http.post(`${h.N.baseURL}/webservice/rest/server.php`,n).pipe((0,x.w)(r=>(0,g.of)(r)))}}return i.\u0275fac=function(e){return new(e||i)(t.LFG(b.eN),t.LFG(m.F0))},i.\u0275prov=t.Yz7({token:i,factory:i.\u0275fac,providedIn:"root"}),i})();var M=d(51),L=d(780),Y=d(1930),Q=d(658),k=d(3416),w=d(7392);const F=function(){return{disabled:"true",cursor:"not-allowed"}},D=function(){return{disabled:"false",cursor:"pointer"}},U=function(){return{color:" #FF8C00"}},j=function(){return{color:"#D0D0D0"}};function z(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"span",2)(1,"div",3)(2,"i",4),t.NdJ("click",function(){const r=t.CHM(e).index,l=t.oxw();return t.KtG(l.onClick(r))}),t.qZA()()()}if(2&i){const e=o.index,s=t.oxw();t.xp6(1),t.Q6J("ngStyle",s.activityCompletionStatus?t.DdM(3,F):t.DdM(4,D)),t.xp6(1),t.Q6J("ngStyle",s.activityCompletionStatus?t.DdM(5,F):t.DdM(6,D))("ngStyle",e<=s.filledIndex?t.DdM(7,U):t.DdM(8,j))}}let O=(()=>{class i{constructor(){this.activityCompletionStatus=!1,this.filledIndex=-1,this.ratingChange=new t.vpe,this.stars=[0,1,2,3,4]}onClick(e){0==this.activityCompletionStatus&&(this.filledIndex=e,this.ratingChange.emit(e+1))}}return i.\u0275fac=function(e){return new(e||i)},i.\u0275cmp=t.Xpm({type:i,selectors:[["app-star"]],inputs:{activityCompletionStatus:"activityCompletionStatus",filledIndex:"filledIndex"},outputs:{ratingChange:"ratingChange"},decls:2,vars:1,consts:[[1,"flex","items-center","gap-1"],["class","text-gray-300 text-4xl",4,"ngFor","ngForOf"],[1,"text-gray-300","text-4xl"],[3,"ngStyle"],[1,"fa","fa-star",3,"ngStyle","click"]],template:function(e,s){1&e&&(t.TgZ(0,"div",0),t.YNc(1,z,3,9,"span",1),t.qZA()),2&e&&(t.xp6(1),t.Q6J("ngForOf",s.stars))},dependencies:[f.sg,f.PC]}),i})();function R(i,o){1&i&&(t.TgZ(0,"div",4),t._UZ(1,"div",5),t.qZA())}function G(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"div",6)(1,"div")(2,"h1",7),t._uU(3),t.ALo(4,"translate"),t.qZA(),t.TgZ(5,"div",8)(6,"button",9),t.NdJ("click",function(){t.CHM(e);const n=t.oxw();return t.KtG(n.goToCourse())}),t._uU(7,"Course Details"),t.qZA()()()()}2&i&&(t.xp6(3),t.hij(" ",t.lcZ(4,1,"YOU ARE NOT ENROLLED IN THIS COURSE")," "))}function E(i,o){if(1&i&&(t.TgZ(0,"div",30)(1,"div",31),t._uU(2),t.TgZ(3,"span",32),t._uU(4,"*"),t.qZA()()()),2&i){const e=t.oxw().$implicit;t.xp6(2),t.Oqu(e.question)}}function P(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"div",33)(1,"app-star",34),t.NdJ("ratingChange",function(n){t.CHM(e);const r=t.oxw().index,l=t.oxw(2);return t.KtG(l.setRating(r,n))}),t.qZA()()}if(2&i){const e=t.oxw().index,s=t.oxw(2);t.xp6(1),t.Q6J("activityCompletionStatus",s.completeDone)("filledIndex",s.feedbacks[e].rating-1)}}function H(i,o){if(1&i&&(t.TgZ(0,"div",27),t.YNc(1,E,5,1,"div",28),t.YNc(2,P,2,2,"div",29),t.qZA()),2&i){const e=o.index;t.xp6(1),t.Q6J("ngIf",1!=e),t.xp6(1),t.Q6J("ngIf",1!=e)}}function B(i,o){1&i&&(t.TgZ(0,"span",32),t._uU(1,"*"),t.qZA())}function $(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"textarea",35),t.NdJ("ngModelChange",function(n){t.CHM(e);const r=t.oxw(2);return t.KtG(r.courseComment.answer=n)}),t._uU(1,"                        "),t.qZA()}if(2&i){const e=t.oxw(2);t.Q6J("ngModel",e.courseComment.answer)}}function K(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"textarea",36),t.NdJ("ngModelChange",function(n){t.CHM(e);const r=t.oxw(2);return t.KtG(r.courseComment.answer=n)}),t._uU(1,"    \n                    "),t.qZA()}if(2&i){const e=t.oxw(2);t.Q6J("disabled",!0)("ngModel",e.courseComment.answer)}}function X(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"button",37),t.NdJ("click",function(){t.CHM(e);const n=t.oxw(2);return t.KtG(n.goPrevActivity())}),t._UZ(1,"mat-icon",38),t.TgZ(2,"span"),t._uU(3),t.ALo(4,"translate"),t.qZA()()}2&i&&(t.xp6(1),t.Q6J("svgIcon","mat_solid:keyboard_arrow_left"),t.xp6(2),t.Oqu(t.lcZ(4,2,"Previous")))}function V(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"button",39),t.NdJ("click",function(){t.CHM(e);const n=t.oxw(2);return t.KtG(n.onSubmit())}),t._uU(1),t.ALo(2,"translate"),t.qZA()}2&i&&(t.xp6(1),t.hij(" ",t.lcZ(2,1,"Submit")," "))}function W(i,o){1&i&&(t.TgZ(0,"button",40),t._uU(1),t.ALo(2,"translate"),t.qZA()),2&i&&(t.Q6J("disabled",!0),t.xp6(1),t.hij(" ",t.lcZ(2,2,"Submit")," "))}function tt(i,o){if(1&i){const e=t.EpF();t.TgZ(0,"button",41),t.NdJ("click",function(){t.CHM(e);const n=t.oxw(2);return t.KtG(n.getCertificate())}),t._uU(1),t.ALo(2,"translate"),t.qZA()}2&i&&(t.xp6(1),t.hij(" ",t.lcZ(2,1,"Download Certificate")," "))}function et(i,o){1&i&&(t.TgZ(0,"div",42),t._UZ(1,"div",5),t.qZA())}function it(i,o){if(1&i&&(t.TgZ(0,"div",10)(1,"div")(2,"div",11),t.YNc(3,H,3,2,"div",12),t.TgZ(4,"div",13)(5,"p",14),t._uU(6),t.YNc(7,B,2,0,"span",15),t.qZA()(),t.YNc(8,$,2,1,"textarea",16),t.YNc(9,K,2,2,"textarea",17),t.qZA(),t.TgZ(10,"div",18)(11,"div",19),t.YNc(12,X,5,4,"button",20),t.YNc(13,V,3,3,"button",21),t.YNc(14,W,3,4,"button",22),t.YNc(15,tt,3,3,"button",23),t.YNc(16,et,2,0,"div",24),t.qZA()(),t.TgZ(17,"div",25)(18,"p",26),t._uU(19),t.qZA()()()()),2&i){const e=t.oxw();t.xp6(3),t.Q6J("ngForOf",e.feedbacks),t.xp6(3),t.hij("",e.courseComment.question," "),t.xp6(1),t.Q6J("ngIf",e.isCommentRequired),t.xp6(1),t.Q6J("ngIf",!e.completeDone),t.xp6(1),t.Q6J("ngIf",e.completeDone),t.xp6(3),t.Q6J("ngIf",-1!=e.prevActivityID&&"customcert"!=e.prevActivityType),t.xp6(1),t.Q6J("ngIf",e.isSubmissionComplete()&&!e.submitSectionLoader&&!e.certificateAvailable),t.xp6(1),t.Q6J("ngIf",!e.isSubmissionComplete()&&!e.submitSectionLoader&&!e.certificateAvailable),t.xp6(1),t.Q6J("ngIf",e.certificateAvailable&&e.completeDone&&!e.submitSectionLoader),t.xp6(1),t.Q6J("ngIf",e.submitSectionLoader),t.xp6(3),t.hij(" ",e.title," ")}}const st=[{path:"",component:(()=>{class i{constructor(e,s,n,r,l,u,c,a,p,y,_,C,I){this._question=e,this._saveFedback=s,this.fb=n,this.route=r,this._router=l,this._authService=u,this.getCertificateApi=c,this.autoDoneApi=a,this.getActivityStatus=p,this.activityApi=y,this.completeCourseService=_,this.activityCompletion=C,this.translateService=I,this.forms=[],this.feedbacks=[],this.enrolled=!1,this.cert_url="",this.commentFillded=!1,this.certificateAvailable=!1,this.isCommentRequired=!1,this.completeDone=!1,this.loading=!0,this.ratingRequired=0,this.ratingSubmitted=0,this.submitSectionLoader=!1,this.courseComment={}}ngOnInit(){this.route.queryParams.subscribe(e=>{this.topicId=e.topic,this.activityID=e.activity,this.courseID=e.course,this.userID=localStorage.getItem("user-id"),this.saveCurrentActivity(this.userID,this.courseID,this.activityID,this.topicId),this.getActivityStatus.getActivityStatus(this.courseID,this.userID).subscribe(s=>{if(s.exception||s.errorcode)return this.enrolled=!1,this.loading=!1,this._authService.signOut(),void this._router.navigate(["login"]);let n=s.statuses;this.enrolled=!1;for(let r of n)r.cmid==this.activityID&&(this.enrolled=!0,0==r.state?this.completeDone=!1:(this.completeDone=!0,this.commentFillded=!0,this.testCertificate(),this.activityCompletion.updateActivityStatus(Number(this.activityID))))}),this.activityApi.getActivities(this.courseID).subscribe(s=>{const n=s.slice(1),r=n?.find(u=>u.id==this.topicId),l=n.findIndex(u=>u.id==this.topicId);this.activityList=r.modules,this.activityList.forEach((u,c)=>{if(u.id==this.activityID)if("bn"===this.translateService.getDefaultLang()?this.activityApi.getBengaliDetailsActivity(u.id).subscribe(a=>{this.title=a.results[0].category_details[0].field_value}):this.title=u.name,this.instanceID=u.instance,c>0)this.activityList[c-1].id&&(this.prevActivityID=this.activityList[c-1].id,this.prevInstanceID=this.activityList[c-1].instance,this.prevActivityType=this.activityList[c-1].modname,"resource"==this.prevActivityType&&(this.prevActivityFormat=this.activityList[c-1].contentsinfo.mimetypes));else if(l-1>=0&&n[l-1].modules?.length>0){const a=n[l-1].modules;this.prevTopicId=n[l-1].id,this.prevActivityID=a[a?.length-1].id,this.prevInstanceID=a[a?.length-1].instance,this.prevActivityType=a[a?.length-1].modname,"resource"==this.prevActivityType&&(this.prevActivityFormat=a[a?.length-1].contentsinfo.mimetypes)}}),this._question.getFeedbackQuestion(this.instanceID,this.activityID).subscribe(u=>{this.feedbacks=u?.questions,this.isCommentRequired=0!=u.iscommentrequired;for(let c=0;c<u.questions?.length;c++)1!=c&&(this.ratingRequired++,this.feedbacks[c].rating=null==u.questions[c].response?0:u.questions[c].response,this.feedbacks[c].rating>0&&this.ratingSubmitted++),1==c&&(this.courseComment.answer=u.questions[c].response,this.courseComment.answer?.length>0&&(this.commentFillded=!0));this.loading=!1,this.courseComment.question=this.feedbacks?.find(c=>0==c.questionid&&"text"==c.inputtype).question,this.completeDone=1==this.isSubmissionComplete()})})})}setRating(e,s){0==this.feedbacks[e].rating&&this.ratingSubmitted++,this.feedbacks[e].rating=s}onSubmit(){1==this.isSubmissionComplete()?(this.completeDone=!0,this.activityCompletion.updateActivityStatus(Number(this.activityID)),this.submitSectionLoader=!0,this._saveFedback.submitFeedback(this.feedbacks,this.courseComment,this.instanceID,this.activityID,this.courseID).subscribe(e=>{this.autoDoneApi.autoDone(this.instanceID).subscribe(s=>{this.testCertificate(),1==this.certificateAvailable&&(this.submitSectionLoader=!1),this.completeCourseService.submit()}),this._question.getFeedbackQuestion(this.instanceID,this.activityID).subscribe(s=>{if(s.exception||s.errorcode)return this.enrolled=!1,this.loading=!1,this._authService.signOut(),void this._router.navigate(["login"]);this.feedbacks=s.questions;for(let n=0;n<s.questions?.length;n++)1!=n&&(this.feedbacks[n].rating=null==s.questions[n].response?0:s.questions[n].response),1==n&&(this.courseComment.answer=s.questions[n].response,this.courseComment.answer?.length>0&&(this.commentFillded=!0));this.loading=!1,this.courseComment.question=this.feedbacks?.find(n=>0==n.questionid&&"text"==n.inputtype).question})})):this.commentFillded=!1}goToCourse(){this._router.navigate(["course",this.courseID])}getCertificate(){window.open(this.cert_url,"_blank")}goPrevActivity(){-1===this.activityList.findIndex(e=>e.id==this.prevActivityID)&&(this.topicId=this.prevTopicId),"video/mp4"==this.prevActivityFormat||"video/webm"==this.prevActivityFormat||"video/mov"==this.prevActivityFormat?this._router.navigate([`course-activity/${this.courseID}/video`],{queryParams:{course:this.courseID,activity:this.prevActivityID,topic:this.topicId}}):"application/pdf"==this.prevActivityFormat?this._router.navigate([`course-activity/${this.courseID}/pdf`],{queryParams:{course:this.courseID,activity:this.prevActivityID,topic:this.topicId}}):"quiz"==this.prevActivityType?this._router.navigate([`course-activity/${this.courseID}/quiz`],{queryParams:{id:this.prevInstanceID,course:this.courseID,activity:this.prevActivityID,topic:this.topicId}}):"zoom"==this.prevActivityType?this._router.navigate([`course-activity/${this.courseID}/meeting`],{queryParams:{course:this.courseID,activity:this.prevActivityID,topic:this.topicId}}):"videoplus"===this.prevActivityType&&this._router.navigate([`course-activity/${this.courseID}/video-pdf`],{queryParams:{course:this.courseID,activity:this.prevActivityID,topic:this.topicId}})}testCertificate(){this.getCertificateApi.getCertificate(this.courseID,this.userID).subscribe(e=>{e.url?(this.certificateAvailable=!0,this.submitSectionLoader=!1,this.cert_url=e.url):this.certificateAvailable=!1})}isSubmissionComplete(){return this.isCommentRequired?this.ratingRequired==this.ratingSubmitted&&this.courseComment.answer?.length>0:this.ratingRequired==this.ratingSubmitted}saveCurrentActivity(e,s,n,r){const l={type:"feedback",activityId:n,topicId:r};localStorage.setItem(JSON.stringify({userId:e,courseId:s}),JSON.stringify(l))}}return i.\u0275fac=function(e){return new(e||i)(t.Y36(T),t.Y36(Z),t.Y36(v.qu),t.Y36(m.gz),t.Y36(m.F0),t.Y36(q.e),t.Y36(N.G),t.Y36(J),t.Y36(M.o),t.Y36(L.I),t.Y36(Y.H),t.Y36(Q.B),t.Y36(k.sK))},i.\u0275cmp=t.Xpm({type:i,selectors:[["app-feedback-activity"]],decls:4,vars:3,consts:[[1,"min-h-screen"],["class","h-screen flex justify-center items-center",4,"ngIf"],["class","min-h-screen flex justify-center items-center",4,"ngIf"],["class","min-h-screen px-2 sm:px-0 lg:px-0 lg:max-w-4xl xl:max-w-7xl py-5 mx-auto",4,"ngIf"],[1,"h-screen","flex","justify-center","items-center"],[1,"loader"],[1,"min-h-screen","flex","justify-center","items-center"],[1,"text-center","text-vumi-orange","text-2xl","font-bold","lg:text-4xl"],[1,"flex","justify-center","mt-7"],["type","button",1,"button-vumi","infxs:text-base","sm:text-lg","infxs:w-30","infxs:h-8","sm:w-[140px]","sm:h-[38px]","text-white","text-lg","bg-blue-700","hover:bg-blue-800","focus:outline-none","focus:ring-4","focus:ring-blue-300","font-medium","text-center","mr-2","mb-2","dark:bg-blue-600","dark:hover:bg-blue-700","dark:focus:ring-blue-800",3,"click"],[1,"min-h-screen","px-2","sm:px-0","lg:px-0","lg:max-w-4xl","xl:max-w-7xl","py-5","mx-auto"],[1,"lg:max-w-4xl","xl:max-w-7xl","mx-auto"],["class","md:flex md:justify-between",4,"ngFor","ngForOf"],[1,""],[1,"text-base","sm:text-lg","md:text-3xl","font-bold","text-vumi-blue","mt-3"],["class","text-vumi-orange font-bold mt-4 ml-1",4,"ngIf"],["class","mt-3 py-4 w-full h-30  md:h-40 p-4 text-lg","style","border:1px solid black;",3,"ngModel","ngModelChange",4,"ngIf"],["class","mt-3 py-4 w-full cursor-not-allowed h-30  md:h-40 p-4 text-lg","style","border:1px solid black;",3,"disabled","ngModel","ngModelChange",4,"ngIf"],[1,"lg:max-w-4xl","xl:max-w-7xl","mb-2","mx-auto"],[1,"infxs:mx-0","xs:mx-15","sm:mx-18","md:mx-20","lg:mx-30","xl:mx-40","gap-2","flex","flex-row","justify-between","lg:mt-4","infxs:mt-4","lg:mb-4","infxs:mb-4"],["style","filter: drop-shadow(0px 4px 4px rgb(35, 6, 1,0.3))","class","button-vumi infxs:text-base sm:text-lg infxs:w-30 infxs:h-8 sm:w-[140px] sm:h-[38px]\n                            bg-vumi-orange text-white font-bold capitalize  outline-none focus:outline-none mb-1 ease-linear transition-all duration-150","type","button",3,"click",4,"ngIf"],["class","button-vumi infxs:text-base sm:text-lg infxs:w-30 infxs:h-8 sm:w-[140px] sm:h-[38px] text-white bg-vumi-blue font-bold capitalize  outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150","type","submit",3,"click",4,"ngIf"],["class","infxs:text-base sm:text-lg infxs:w-30 infxs:h-8 sm:w-[140px] sm:h-[38px] cursor-not-allowed bg-gray-300 font-bold capitalize  outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150","type","submit",3,"disabled",4,"ngIf"],["class","infxs:px-2 button-vumi infxs:text-base sm:text-lg border-vumi-blue bg-vumi-blue text-white font-bold capitalize \n                             outline-none focus:outline-none mr-1 mb-1 ease-linear \n                            transition-all duration-150","type","button",3,"click",4,"ngIf"],["class","flex-end items-center",4,"ngIf"],[1,"mb-4"],[1,"text-left","text-vumi-black","font-semibold","lg:text-4xl","sm:text-5xl","infxs:text-3xl"],[1,"md:flex","md:justify-between"],["class","mt-5 md:mt-0 md:flex md:justify-center md:items-center",4,"ngIf"],["class","flex flex-wrap md:start md:rating mb-5",4,"ngIf"],[1,"mt-5","md:mt-0","md:flex","md:justify-center","md:items-center"],[1,"text-base","sm:text-lg","md:text-3xl","font-bold","text-vumi-blue"],[1,"text-vumi-orange","font-bold","mt-4","ml-1"],[1,"flex","flex-wrap","md:start","md:rating","mb-5"],[3,"activityCompletionStatus","filledIndex","ratingChange"],[1,"mt-3","py-4","w-full","h-30","md:h-40","p-4","text-lg",2,"border","1px solid black",3,"ngModel","ngModelChange"],[1,"mt-3","py-4","w-full","cursor-not-allowed","h-30","md:h-40","p-4","text-lg",2,"border","1px solid black",3,"disabled","ngModel","ngModelChange"],["type","button",1,"button-vumi","infxs:text-base","sm:text-lg","infxs:w-30","infxs:h-8","sm:w-[140px]","sm:h-[38px]","bg-vumi-orange","text-white","font-bold","capitalize","outline-none","focus:outline-none","mb-1","ease-linear","transition-all","duration-150",2,"filter","drop-shadow(0px 4px 4px rgb(35, 6, 1,0.3))",3,"click"],["aria-hidden","false","aria-label","previous activity icon",1,"text-white","icon-size-7",3,"svgIcon"],["type","submit",1,"button-vumi","infxs:text-base","sm:text-lg","infxs:w-30","infxs:h-8","sm:w-[140px]","sm:h-[38px]","text-white","bg-vumi-blue","font-bold","capitalize","outline-none","focus:outline-none","mr-1","mb-1","ease-linear","transition-all","duration-150",3,"click"],["type","submit",1,"infxs:text-base","sm:text-lg","infxs:w-30","infxs:h-8","sm:w-[140px]","sm:h-[38px]","cursor-not-allowed","bg-gray-300","font-bold","capitalize","outline-none","focus:outline-none","mr-1","mb-1","ease-linear","transition-all","duration-150",3,"disabled"],["type","button",1,"infxs:px-2","button-vumi","infxs:text-base","sm:text-lg","border-vumi-blue","bg-vumi-blue","text-white","font-bold","capitalize","outline-none","focus:outline-none","mr-1","mb-1","ease-linear","transition-all","duration-150",3,"click"],[1,"flex-end","items-center"]],template:function(e,s){1&e&&(t.TgZ(0,"div",0),t.YNc(1,R,2,0,"div",1),t.YNc(2,G,8,3,"div",2),t.YNc(3,it,20,11,"div",3),t.qZA()),2&e&&(t.xp6(1),t.Q6J("ngIf",s.loading),t.xp6(1),t.Q6J("ngIf",!s.enrolled&&!s.loading),t.xp6(1),t.Q6J("ngIf",!s.loading&&s.enrolled))},dependencies:[f.sg,f.O5,v.Fj,v.JJ,v.On,w.Hw,O,k.X$],styles:[".loader[_ngcontent-%COMP%]{border:14px solid #f3f3f3;border-top:8px solid #3498db;border-radius:50%;width:60px;height:60px;animation:spin .5s linear infinite}"]}),i})()}];let nt=(()=>{class i{}return i.\u0275fac=function(e){return new(e||i)},i.\u0275mod=t.oAB({type:i}),i.\u0275inj=t.cJS({imports:[f.ez,v.UX,m.Bz.forChild(st),S.m,w.Ps]}),i})()}}]);