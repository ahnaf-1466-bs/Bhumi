import { HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
    providedIn: 'root',
})
export class HelperService {
    constructor() {}
    /**
     *
     * @param data
     * @param param
     * @returns a formData
     */
    formMaker(data: any, param: string = ''): FormData {
        // console.log(data, param);
        let form = new FormData();
        let pairs: any[] = this.Go(data);
        for (let pair of pairs) {
            form.append(pair.a, pair.b);
        }

        return form;
    }

    /**
     *
     * @param obj
     * @param form
     * @param namespace
     * @returns recursive form to deal with nested object
     */

    Go(data: any = {}, map: any | null = null, key = '') {
        let ret: any[] = map || [];
        let formKey: any;
        for (let prop in data) {
            if (data.hasOwnProperty(prop)) {
                if (key) {
                    formKey = `${key}[${prop}]`;
                } else {
                    formKey = prop;
                }
                if (data[prop] instanceof Array) {
                    data[prop].forEach((el: any, id: number) => {
                        if (typeof el === 'object') {
                            let temp = this.Go(el, map, `${formKey}[${id}]`);
                            for (let x of temp) {
                                ret.push({ a: x.a, b: x.b });
                            }
                        } else {
                            let a = `${formKey}[${id}]`;
                            ret.push({ a: a, b: el });
                        }
                    });
                } else if (typeof data[prop] === 'object') {
                    let temp = this.Go(data[prop], ret, formKey);
                    for (let x of temp) {
                        ret.push({ a: x.a, b: x.b });
                    }
                } else {
                    ret.push({ a: formKey, b: data[prop] });
                }
            }
        }
        return ret;
    }

    httpMaker(data: any): HttpParams {
        let tkn = localStorage.getItem('token');
        let token = '';
        if (tkn !== null) {
            token = JSON.parse(tkn).token;
        }
        let httpParams = new HttpParams()
            .set('wstoken', token)
            .set('moodlewsrestformat', 'json');
        Object.keys(data).forEach((key) => {
            if (typeof data[key] === 'object') {
            }
            httpParams = httpParams.set(key, data[key]);
        });
        // console.log(httpParams);
        return httpParams;
    }
}
