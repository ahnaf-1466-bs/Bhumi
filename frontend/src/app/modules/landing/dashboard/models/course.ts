export interface Course{
    id:string;
    picurl: string;
    fullname:string;
    summary:string;
    favourite:boolean;
    progress?:number;
}