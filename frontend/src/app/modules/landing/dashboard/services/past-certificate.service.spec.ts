import { TestBed } from '@angular/core/testing';

import { PastCertificateService } from './past-certificate.service';

describe('PastCertificateService', () => {
  let service: PastCertificateService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PastCertificateService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
