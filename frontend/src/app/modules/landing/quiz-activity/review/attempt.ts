export interface attempt {
    id: number;
    quiz: string;
    userid: number;
    attempt: number;
    uniqueid: number;
    layout: number;
    currentpage: number;
    preview: number;
    state: string;
    timestart: number;
    timefinish: number;
    timemodified: number;
    timemodifiedoffline: number;
    timecheckstate: string | null;
    sumgrades: number;
}
