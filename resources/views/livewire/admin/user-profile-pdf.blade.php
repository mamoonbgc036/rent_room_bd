<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 100%;
            max-width: 800px; /* Adjust as necessary */
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Two columns */
            gap: 20px; /* Gap between grid items */
        }
        .grid-item {
            background-color: #f0f0f0;
            padding: 20px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info strong {
            display: block;
            margin-bottom: 5px;
        }
        .profile-info p {
            margin: 0 0 10px;
        }
        .proof-files {
            margin-top: 20px;
        }
        .proof-files strong {
            display: block;
            margin-bottom: 5px;
        }
        .proof-files img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile - {{ $user->name }}</h2>

        <div class="grid-container">
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Name:</strong>
                    <p>{{ $user->name }}</p>

                    <strong>Email:</strong>
                    <p>{{ $user->email }}</p>

                    <strong>Phone:</strong>
                    <p>{{ $user->phone }}</p>
                </div>
            </div>
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Photo ID Proof Type:</strong>
                    <p>{{ ucfirst($user->photo_id_proof_type) }}</p>

                    @if ($user->photo_id_proof_path)
                    @php
                        $imagePathPhotoProof = storage_path('app/public/' . $user->photo_id_proof_path);
                        $imageDataPhotoProof = base64_encode(file_get_contents($imagePathPhotoProof));
                        $imageSrcPhotoProof = 'data:'.mime_content_type($imagePathPhotoProof).';base64,'.$imageDataPhotoProof;
                    @endphp

                    <div class="proof-files">
                        <strong>Photo ID Proof File:</strong>
                        <img src="{{ $imageSrcPhotoProof }}" alt="Photo ID Proof">
                    </div>
                    @endif
                </div>
            </div>
            <div class="grid-item">
                <div class="profile-info">
                    <strong>User Proof Type:</strong>
                    <p>{{ ucfirst($user->user_proof_type) }}</p>

                    @if ($user->user_proof_path)
                    @php
                        $imagePathUserProof = storage_path('app/public/' . $user->user_proof_path);
                        $imageDataUserProof = base64_encode(file_get_contents($imagePathUserProof));
                        $imageSrcUserProof = 'data:'.mime_content_type($imagePathUserProof).';base64,'.$imageDataUserProof;
                    @endphp

                    <div class="proof-files">
                        <strong>User Proof File:</strong>
                        <img src="{{ $imageSrcUserProof }}" alt="User Proof">
                    </div>
                    @endif
                </div>
            </div>
            @if($user->proof_type_1 && $user->proof_path_1)
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Proof Type 1:</strong>
                    <p>{{ ucfirst($user->proof_type_1) }}</p>

                    @php
                        $imagePathProof1 = storage_path('app/public/' . $user->proof_path_1);
                        $imageDataProof1 = base64_encode(file_get_contents($imagePathProof1));
                        $imageSrcProof1 = 'data:'.mime_content_type($imagePathProof1).';base64,'.$imageDataProof1;
                    @endphp

                    <div class="proof-files">
                        <strong>Proof File 1:</strong>
                        <img src="{{ $imageSrcProof1 }}" alt="Proof 1">
                    </div>
                </div>
            </div>
            @endif

            @if($user->proof_type_2 && $user->proof_path_2)
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Proof Type 2:</strong>
                    <p>{{ ucfirst($user->proof_type_2) }}</p>

                    @php
                        $imagePathProof2 = storage_path('app/public/' . $user->proof_path_2);
                        $imageDataProof2 = base64_encode(file_get_contents($imagePathProof2));
                        $imageSrcProof2 = 'data:'.mime_content_type($imagePathProof2).';base64,'.$imageDataProof2;
                    @endphp

                    <div class="proof-files">
                        <strong>Proof File 2:</strong>
                        <img src="{{ $imageSrcProof2 }}" alt="Proof 2">
                    </div>
                </div>
            </div>
            @endif

            @if($user->proof_type_3 && $user->proof_path_3)
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Proof Type 3:</strong>
                    <p>{{ ucfirst($user->proof_type_3) }}</p>

                    @php
                        $imagePathProof3 = storage_path('app/public/' . $user->proof_path_3);
                        $imageDataProof3 = base64_encode(file_get_contents($imagePathProof3));
                        $imageSrcProof3 = 'data:'.mime_content_type($imagePathProof3).';base64,'.$imageDataProof3;
                    @endphp

                    <div class="proof-files">
                        <strong>Proof File 3:</strong>
                        <img src="{{ $imageSrcProof3 }}" alt="Proof 3">
                    </div>
                </div>
            </div>
            @endif

            @if($user->proof_type_4 && $user->proof_path_4)
            <div class="grid-item">
                <div class="profile-info">
                    <strong>Proof Type 4:</strong>
                    <p>{{ ucfirst($user->proof_type_4) }}</p>

                    @php
                        $imagePathProof4 = storage_path('app/public/' . $user->proof_path_4);
                        $imageDataProof4 = base64_encode(file_get_contents($imagePathProof4));
                        $imageSrcProof4 = 'data:'.mime_content_type($imagePathProof4).';base64,'.$imageDataProof4;
                    @endphp

                    <div class="proof-files">
                        <strong>Proof File 4:</strong>
                        <img src="{{ $imageSrcProof4 }}" alt="Proof 4">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
