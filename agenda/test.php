<?php
function validate_cpf($cpf) {
  // Remove any non-numeric characters from the CPF
  $cpf = preg_replace('/[^0-9]/', '', $cpf);

  // Check if the CPF has 11 digits
  if (strlen($cpf) != 11) {
    return false;
  }

  // Check if all digits are the same
  if (preg_match('/(\d)\1{10}/', $cpf)) {
    return false;
  }

  // Validate the CPF digits using the formula
  $sum = 0;
  for ($i = 0; $i < 9; $i++) {
    $sum += ($cpf[$i] * (10 - $i));
  }
  $remainder = $sum % 11;

  if ($remainder == 1 || $remainder == 0) {
    $digit1 = 0;
  } else {
    $digit1 = 11 - $remainder;
  }

  $sum = 0;
  for ($i = 0; $i < 9; $i++) {
    $sum += ($cpf[$i] * (11 - $i));
  }
  $sum += ($digit1 * 2);
  $remainder = $sum % 11;

  if ($remainder == 1 || $remainder == 0) {
    $digit2 = 0;
  } else {
    $digit2 = 11 - $remainder;
  }

  // Check if the CPF is valid
  if ($cpf[9] == $digit1 && $cpf[10] == $digit2) {
    return true;
  } else {
    return false;
  }
}


$cpf = "158.645.417-02";
if (validate_cpf($cpf)) {
  echo "Valid CPF";
} else {
  echo "Invalid CPF";
}