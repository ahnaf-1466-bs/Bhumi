import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PastCertificateComponent } from './past-certificate.component';

describe('PastCertificateComponent', () => {
  let component: PastCertificateComponent;
  let fixture: ComponentFixture<PastCertificateComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PastCertificateComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PastCertificateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
