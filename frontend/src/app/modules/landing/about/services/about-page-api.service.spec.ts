import { TestBed } from '@angular/core/testing';

import { AboutPageApiService } from './about-page-api.service';

describe('AboutPageApiService', () => {
  let service: AboutPageApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AboutPageApiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
