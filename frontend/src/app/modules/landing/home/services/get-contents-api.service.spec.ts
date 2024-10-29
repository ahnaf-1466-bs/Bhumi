import { TestBed } from '@angular/core/testing';

import { GetContentsApiService } from './get-contents-api.service';

describe('GetContentsApiService', () => {
  let service: GetContentsApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GetContentsApiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
