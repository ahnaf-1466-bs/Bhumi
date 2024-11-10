import { TestBed } from '@angular/core/testing';

import { AutoDoneService } from './auto-done.service';

describe('AutoDoneService', () => {
  let service: AutoDoneService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AutoDoneService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
