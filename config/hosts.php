<?php

return [
    [
        'id' => 1,
        'user_id' => 1,
        'hostname' => 'sri.nic.arpa', 
        'password' => random_pass(),
        'org' => 'Standford Research Institute (NIC)',
        'location' => 'Menlo Park, CA',
        'welcome' => "",
        'os' => 'VAX-11/750 TOPS-20',
        'ip' => '192.5.4.1',
        'network' => 1,
        'level_id' => 1,
        'motd' => null,
        'created_at' => timestamp("1969-10-30 06:30:00", true)
    ],
    [
        'id' => 2,
        'user_id' => 1,
        'hostname' => 'ucla.arpa', 
        'password' => random_pass(),
        'org' => 'University of California',
        'location' => 'Los Angelos, CA',
        'welcome' => 'Authorized users only.',
        'os' => 'VAX-11/780 4.3BSD UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 1,
        'motd' => null,
        'created_at' => timestamp("1969-10-30 06:30:00", true)
    ],
    [
        'id' => 3,
        'user_id' => 1,
        'hostname' => 'ucsb.arpa', 
        'password' => random_pass(),
        'org' => 'University of California',
        'location' => 'Santa Barbara, CA',
        'welcome' => 'Authorized users only.',
        'os' => 'VAX-11/780 4.3BSD UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 1,
        'motd' => '',
        'created_at' => timestamp("1969-10-30 06:30:00", true)
    ],
    [
        'id' => 4,
        'user_id' => 1,
        'hostname' => 'uusc.arpa', 
        'password' => random_pass(),
        'org' => 'University of Utah School of Computing',
        'location' => 'Salt Lake City, UT',
        'welcome' => 'Authorized users only.',
        'os' => 'VAX-11/750 4.3BSD UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 1,
        'motd' => '',
        'created_at' => timestamp("1969-10-30 06:30:00", true)
    ],
    [
        'id' => 5,
        'user_id' => 1,
        'hostname' => 'nic.ddn.mil', 
        'password' => random_pass(8),
        'org' => 'Military Defense Data Network (UNCLASSIFIED)',
        'location' => '',
        'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
        'os' => 'UNIX System V AT&T',
        'ip' => '192.67.67.20',
        'network' => 1,
        'level_id' => 3,
        'motd' => 'WARNING: All activity may be monitored and recorded.',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 6,
        'user_id' => 1,
        'hostname' => 'dsnet1.mil', 
        'password' => random_pass(8),
        'org' => 'Defense Secure Network 1 (CONFIDENTIAL)',
        'location' => '',
        'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
        'os' => 'VAX/VMS UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 4,
        'motd' => 'WARNING: All activity may be monitored and recorded.',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 7,
        'user_id' => 1,
        'hostname' => 'dsnet2.mil', 
        'password' => random_pass(8),
        'org' => 'Defense Secure Network 2 (SECRET)',
        'location' => '',
        'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
        'os' => 'VAX/VMS UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 5,
        'motd' => 'WARNING: All activity may be monitored and recorded.',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 8,
        'user_id' => 1,
        'hostname' => 'dsnet3.mil', 
        'password' => random_pass(8),
        'org' => 'Defense Secure Network 3 (TOP SECRET)',
        'location' => '',
        'welcome' => '',
        'os' => 'VAX/VMS UNIX',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 6,
        'motd' => 'WARNING: All activity may be monitored and recorded.',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 9,
        'user_id' => 1,
        'hostname' => 'telenet.com', 
        'password' => random_pass(),
        'org' => 'Telenet Inc. (BBN)',
        'location' => '',
        'welcome' => '',
        'os' => 'PDP-11/IMP 4.3BSD UNIX',
        'ip' => random_ip(),
        'network' => 1,
        'level_id' => 1,
        'motd' => '',
        'created_at' => timestamp("1975-01-01 06:30:00", true)
    ],
    [
        'id' => 10,
        'user_id' => 1,
        'hostname' => 'poseido.net', 
        'password' => random_pass(),
        'org' => 'Poseidon Energy Network',
        'location' => 'Commonwealth, Boston',
        'welcome' => 'Begin your Odyssey with us - Ruler of the tides of innovation!',
        'os' => 'RobCo UOS v.84',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 2,
        'motd' => '',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 11,
        'user_id' => 1,
        'hostname' => 'spsdd.edu', 
        'password' => 'pencil',
        'org' => 'PUBLIC SCHOOL DISTRICT',
        'location' => 'Seattle',
        'welcome' => "WELCOME TO THE SEATTLE PUBLIC SCHOOL DISTRICT DATANET.\n\nPLEASE LOGON WITH USER PASSWORD:",
        'os' => 'PDP-11/272 PRS TIP # 45',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 2,
        'motd' => 'Welcome to SEATTLE PUBLIC SCHOOL DISTRICT',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ],
    [
        'id' => 999,
        'user_id' => 1,
        'hostname' => 'wopr.mil', 
        'password' => 'Joshua',
        'org' => '',
        'location' => '',
        'welcome' => "",
        'os' => '* 0x0000A4 0x0000A4 0x00000000000000000 0x000000000000E003D 0x000014 0x000009
        CPUO launch EFIO 0x0000A4 0x000014 0x00000000000000000 1 CPUO starting cell
        relocation 1 0x0000A4 1 1 CPUO starting EFIO 0x0000A4 0x00000000000000000 0
        0x0000A4 CPUO starting EFIO 1 0 0x000009 CPUO starting cell relocation
        0x00000000000000000 0x000000000000E003D 1 CPUO launch EFIO 0
        0x000000000000E003D 0x000009 0x000014 0x00000000000000000 0 0x0000A4 CPUO
        launch EFIO 0x00000000000000000 0x000000000000E003D 0x0000A4 0
        0x000000000000E003D 0x000014 0x000009 CPUO starting cell relocation
        0x000000000000E003D 0x000014 0x000009 0 0x000000000000E003D 0x000000000000E003D 
        0 CPUO starting EFIO 0x000009 0x000000000000E003D 0x00000000000000000 0 0
        0x000014 CPUO starting cell relocation',
        'ip' => random_ip(),
        'network' => 0,
        'level_id' => 6,
        'motd' => '',
        'created_at' => timestamp("1983-01-01 06:30:00", true)
    ]
];