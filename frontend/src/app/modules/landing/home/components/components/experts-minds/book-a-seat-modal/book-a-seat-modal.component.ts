import { Component } from "@angular/core";
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import { MatDialogRef } from "@angular/material/dialog";

export interface SeatBookType {
    name: string;
    email: string;
    phone: string;
  }

@Component({
    selector: 'book-a-seat-modal',
    styleUrls:['./book-a-seat-modal.component.scss'],
    templateUrl: './book-a-seat-modal.component.html',
  })
  export class BookASeatModal{
    form: FormGroup;

    constructor(
      public dialogRef: MatDialogRef<BookASeatModal>,
      private fb: FormBuilder,
    ) {
      this.form = this.fb.group({
        name: ['', Validators.required],
        email: ['', [Validators.required, Validators.email]],
        phone: ['', [Validators.pattern('(?:(?:\\+|00)88|01)?\\d{11}'), Validators.required]],
      });
    }

    submitForm() {
      if (this.form.valid) {
        this.dialogRef.close(this.form.value);
      }
    }
  
    onNoClick(): void {
      this.dialogRef.close();
    }
  }