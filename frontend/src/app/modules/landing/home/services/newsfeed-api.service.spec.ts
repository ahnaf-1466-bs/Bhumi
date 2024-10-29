import { TestBed } from '@angular/core/testing';

import { NewsfeedApiService } from './newsfeed-api.service';

describe('NewsfeedApiService', () => {
  let service: NewsfeedApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(NewsfeedApiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
