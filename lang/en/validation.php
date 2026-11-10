<?php
return [
    'array' => 'The :attribute must be a list.', 'date' => 'The :attribute is not a valid date.', 'digits' => 'The :attribute must be :digits digits.',
    'email' => 'The :attribute must be a valid email address.', 'exists' => 'The selected :attribute is invalid.', 'file' => 'The :attribute must be a file.',
    'image' => 'The :attribute must be an image (jpg, png, webp, or gif).', 'in' => 'The selected :attribute is invalid.', 'integer' => 'The :attribute must be an integer.',
    'mimes' => 'The :attribute must be of type: :values.', 'numeric' => 'The :attribute must be a number.', 'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute is required.', 'starts_with' => 'The :attribute must start with: :values.', 'string' => 'The :attribute must be text.',
    'unique' => 'The :attribute has already been taken.', 'url' => 'The :attribute format is invalid (must start with https://).',
    'uploaded' => 'The :attribute failed to upload. The file is probably larger than the server limit (upload_max_filesize); reduce its size or raise the limit in php.ini.',
    'current_password' => 'The current password is incorrect.', 'confirmed' => 'The :attribute confirmation does not match.',
    'between' => ['numeric' => 'The :attribute must be between :min and :max.', 'file' => 'The :attribute must be between :min and :max KB.', 'string' => 'The :attribute must be between :min and :max characters.', 'array' => 'The :attribute must have between :min and :max items.'],
    'max' => ['numeric' => 'The :attribute must not be greater than :max.', 'file' => 'The :attribute must not be greater than :max KB.', 'string' => 'The :attribute must not be greater than :max characters.', 'array' => 'The :attribute must not have more than :max items.'],
    'min' => ['numeric' => 'The :attribute must be at least :min.', 'file' => 'The :attribute must be at least :min KB.', 'string' => 'The :attribute must be at least :min characters.', 'array' => 'The :attribute must have at least :min items.'],
    'attributes' => [],
];
